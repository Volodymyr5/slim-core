<?php

namespace App\Core\Libs;
use App\MVC\Entity\TokenEntity;
use App\MVC\Models\Token;
use App\MVC\Models\User;
use Firebase\JWT\JWT;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Class Auth
 * @package App\Core\Libs
 */
class Auth
{
    protected $container;

    protected $publicKey;

    protected $privateKey;

    protected $updateExpiration;

    protected $sessionExpiration;

    protected $authExpiration;

    protected $visitorExpiration;

    /**
     * Auth constructor.
     * @param ContainerInterface $container
     * @throws \Exception
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $config = isset($container->settings['custom']) ? $this->container->settings['custom'] : [];

        $this->publicKey = !empty($config['jwt']['public']) ? $config['jwt']['public'] : null;
        $this->privateKey = !empty($config['jwt']['private']) ? $config['jwt']['private'] : null;

        $this->updateExpiration = !empty($config['jwt']['update_expiration']) ? $config['jwt']['update_expiration'] : null;
        $this->sessionExpiration = !empty($config['jwt']['session_expiration']) ? $config['jwt']['session_expiration'] : null;
        $this->authExpiration = !empty($config['jwt']['auth_expiration']) ? $config['jwt']['auth_expiration'] : null;
        $this->visitorExpiration = !empty($config['jwt']['visitor_expiration']) ? $config['jwt']['visitor_expiration'] : null;

        if (empty($this->publicKey) || empty($this->privateKey)) {
            throw new \Exception('Error. Public or private token not set in local.php!');
        }

        if (
            empty($this->updateExpiration) ||
            empty($this->sessionExpiration) ||
            empty($this->authExpiration) ||
            empty($this->visitorExpiration)
        ) {
            throw new \Exception('Error. JWT Token Expiration not set in local.php!');
        }
    }

    public function identity()
    {
        $u = new User();

        $token = $this->getTokenFromCookie();
        $lastSession = $this->getNeedSession($token);
        $users = [];
        if (!empty($lastSession['user_id'])) {
            $users = $u->getAll(['ids' => [$lastSession['user_id']]]);
        }

        var_dump($users);
    }

    /**
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public function login($userId)
    {
        $t = new Token();

        $token = $this->getTokenFromCookie();
        $lastSession = $this->getNeedSession($token);

        \App\Core\Libs\Logger::log(print_r($lastSession, true));

        if ($lastSession) {
            $te = new TokenEntity();
            $te->setId($lastSession['id']);
            $te->setUserId($userId);

            $t->modify($te);
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function logout()
    {
        $t = new Token();

        $token = $this->getTokenFromCookie();
        $lastSession = $this->getNeedSession($token);
        if ($lastSession) {
            $te = new TokenEntity();
            $te->setId($lastSession['id']);
            $te->setUserId('');

            $t->modify($te);
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function update()
    {
        $token = $this->getTokenFromCookie();
        // Check if has token
        if ($token) {
            $tokenArray = $this->readToken($token);
            // Check token
            if (!$this->checkTokenInDB($token)) {
                $this->fraudAttempt($token);
                header("Refresh:0");
                return false;
            }

            // Check Update Expiration
            if ($tokenArray['UE'] <= time()) {
                // Check Session Expiration
                $newSession = $tokenArray['SE'] <= time();
                // Check Auth Expiration
                $clearUser = $tokenArray['AE'] <= time();

                // Update token
                $rawToken = $this->updateTokenInDB($token, $clearUser, $newSession);
                $this->setTokenInCookie($rawToken);
            }
        } else {
            // Create new token
            $rawToken = $this->updateTokenInDB(null, true, true);
            $this->setTokenInCookie($rawToken);
        }

        return true;
    }

    /**
     * @param $token
     * @return bool
     */
    private function checkTokenInDB($token)
    {
        $t = new Token();

        $dbTokensCount = count($t->getAll([
            'token' => $token,
        ]));

        return $dbTokensCount == 1;
    }

    /**
     * @param $oldToken
     * @param bool $clearUser
     * @param bool $newSession
     * @return mixed
     * @throws \Exception
     */
    private function updateTokenInDB($oldToken, $clearUser = false, $newSession = false)
    {
        $t = new Token();

        $newToken = $this->createNewToken($oldToken);

        $te = new TokenEntity();
        $te->exchangeArray([
            'user_id' => '',
            'visitor' => md5($this->getIpForToken() . $this->getBrowserForToken()),
            'token' => $newToken,
            'ip' => $this->getIpForToken(),
            'browser' => $this->getBrowserForToken(),
            'end' => time(),
            'expire' => time() + $this->visitorExpiration,
        ]);

        $prevSession = $this->getNeedSession($oldToken);

        if (!$clearUser && $prevSession) {
            $te->setUserId($prevSession['user_id']);
        }

        if ($prevSession && !$newSession) {
            $te->setId($prevSession['id']);
            $t->modify($te);
        } else {
            $t->create($te);
        }

        return $te->getToken();
    }

    /**
     * @param $oldToken
     * @return object|null
     */
    private function getNeedSession($oldToken)
    {
        $t = new Token();

        $oldToken = $oldToken ? $oldToken : '';

        $session = $t->getAll([
            'token' => $oldToken,
            'ip' => $this->getIpForToken(),
            'browser' => $this->getBrowserForToken(),
            'expire' => time(),
            'sort' => 'id',
            'order' => 'desc',
        ]);

        $session = !empty($session[0]) ? $session[0] : null;
        $session = !empty($session->token) ? $session->asArray() : null;

        return $session;
    }

    /**
     * @param null $token
     * @return bool|string
     */
    private function createNewToken($token = null)
    {
        if ($token) {
            $token = !is_array($token) ? $this->readToken($token) : $token;
            $userIp = !empty($token['TP']) ? $token['TP'] : false;
            $userAgent = !empty($token['TB']) ? $token['TB'] : false;
        } else {
            $userIp = $this->getIpForToken();
            $userAgent = $this->getBrowserForToken();

            $userIp = $this->hashString($userIp);
            $userAgent = $this->hashString($userAgent);
        }

        if (
            empty($userIp) ||
            empty($userAgent)
        ) {
            return false;
        }

        $tokenArray = [
            'TP' => $userIp,
            'TB' => $userAgent,
            'UE' => intval(date('U') + $this->updateExpiration),
            'SE' => intval(date('U') + $this->sessionExpiration),
            'AE' => intval(date('U') + $this->authExpiration),
            'VE' => intval(date('U') + $this->visitorExpiration),
        ];

        return $this->createToken($tokenArray);
    }

    /**
     * @param $tokenArray
     * @return bool|string
     */
    private function createToken($tokenArray)
    {
        try {
            return JWT::encode($tokenArray, $this->privateKey, 'RS256');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $token
     * @return array|null|object
     */
    public function readToken($token)
    {
        try {
            $tokenArray = JWT::decode($token, $this->publicKey, array('RS256'));
            $tokenArray = (array)$tokenArray;
            return $tokenArray;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $token
     * @return bool
     */
    private function setTokenInCookie($token)
    {
        if (empty($token)) {
            return false;
        }

        setcookie('SESSID', $token, time() + $this->visitorExpiration, '/');

        return true;
    }

    /**
     * @return array|null
     */
    private function getTokenFromCookie()
    {
        try {
            $request = $this->container->get('request');
            $cookies = $request->getCookieParams();
        } catch (ContainerExceptionInterface $e) {
            $result = [];
        }

        return !empty($cookies['SESSID']) ? $cookies['SESSID'] : null;
    }

    /**
     * Remove token from Cookie
     */
    private function clearCookie()
    {
        setcookie('SESSID', '', time() - 3600, '/');
    }

    /**
     * @return string|bool
     */
    private function getBrowserForToken()
    {
        if ($this->container->request->hasHeader('HTTP_USER_AGENT')) {
            $agent = $this->container->request->getHeader('HTTP_USER_AGENT');
        }

        if (!empty($agent[0])) {
            return $agent[0];
        }

        return false;
    }

    /**
     * @return string|bool
     */
    private function getIpForToken()
    {
        $ip = $this->container->request->getServerParam('REMOTE_ADDR');
        if (!$ip) {
            return false;
        }

        return $ip;
    }

    /**
     * @param $token
     */
    private function fraudAttempt($token)
    {
        //$token = $this->readToken($token);
        $this->clearCookie();
        // clearTokenFromDB
        //clearHistory
        unset($_SESSION['SESSID']);
    }

    /**
     * @param $string
     * @return string
     */
    private function hashString($string)
    {
        return hash('sha256', $string);
    }
}