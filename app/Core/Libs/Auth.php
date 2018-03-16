<?php

namespace App\Core\Libs;
use App\MVC\Entity\TokenEntity;
use App\MVC\Models\Token;
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
        $t = new Token();

        var_dump(date('Y-m-d H:i:s', time()));
    }

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

            $showInfo = [
                'UE' => isset($tokenArray['UE']) ? date('H:i:s d.m.Y', $tokenArray['UE']) : '',
                'SE' => isset($tokenArray['SE']) ? date('H:i:s d.m.Y', $tokenArray['SE']) : '',
                'AE' => isset($tokenArray['AE']) ? date('H:i:s d.m.Y', $tokenArray['AE']) : '',
                'VE' => isset($tokenArray['VE']) ? date('H:i:s d.m.Y', $tokenArray['VE']) : '',
            ];
            var_dump($showInfo);

            // Check Update Expiration
            if ($tokenArray['UE'] <= time()) {
                // Check Session Expiration
                $newSession = $tokenArray['SE'] <= time();
                // Check Auth Expiration
                $clearUser = $tokenArray['AE'] <= time();

                $rawToken = $this->updateTokenInDB($token, $clearUser, $newSession);
                $this->setTokenInCookie($rawToken);
            }
        } else {
            $rawToken = $this->updateTokenInDB(null, true, true);
            $this->setTokenInCookie($rawToken);
        }
    }

    private function checkTokenInDB($token)
    {
        $t = new Token();

        $dbTokensCount = count($t->getAll([
            'token' => $token,
        ]));

        return $dbTokensCount == 1;
    }

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

        $prevSession = $t->getAll([
            'token' => $oldToken,
            'ip' => $this->getIpForToken(),
            'browser' => $this->getBrowserForToken(),
            'expire' => time(),
            'limit' => 1,
            'sort' => 'id',
            'order' => 'desc',
        ]);

        var_dump('$prevSession', $prevSession);

        $prevSession = !empty($prevSession[0]) ? $prevSession[0] : null;
        $prevSession = !empty($prevSession->token) ? $prevSession : null;

        if (!$clearUser && $prevSession) {
            $te->setUserId($prevSession->user_id);
        }

        if ($prevSession) {
            var_dump(333, $prevSession->id);
        } else {
            var_dump($prevSession);
        }

        if ($prevSession && !$newSession) {
            $t->modify($te);
        } else {
            $t->create($te);
        }

        return $te->getToken();
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

    private function setTokenInCookie($token)
    {
        if (empty($token)) {
            return false;
        }

        setcookie('SESSID', $token, time() + $this->visitorExpiration, '/');

        return true;
    }

    /**
     * @return array
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
     *
     */
    private function clearCookie()
    {
        setcookie('SESSID', '', time() - 3600, '/');
    }

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

    private function hashString($string)
    {
        return hash('sha256', $string);
    }

    /*
    public function update_OLD()
    {
        $tokens = $this->getTokensFromCookie();
        if ($tokens['refresh_token'] && !$this->checkTokenExpire($tokens['refresh_token'])) {
            if (!$this->checkToken($tokens['refresh_token'])) {
                $this->fraudAttempt($tokens['refresh_token']);
            }

            if ($tokens['access_token'] && !$this->checkTokenExpire($tokens['access_token'])) {
                if (!$this->checkToken($tokens['access_token'])) {
                    $this->fraudAttempt($tokens['refresh_token']);
                }

                return true;
            } else {
                $tokens = $this->createTokens($tokens['refresh_token']);
                if (!$tokens) {
                    return false;
                }
                $this->updateTokenInDB($tokens['refresh_token'], true);
                $this->setCookies($tokens);
            }
        } else {
            $tokens = $this->createTokens();
            if (!$tokens) {
                return false;
            }
            $this->updateTokenInDB($tokens['refresh_token']);
            $this->setCookies($tokens);
        }

        if (empty($tokens['access_token']) || empty($tokens['refresh_token'])) {
            return false;
        }

        return true;
    }

    private function setCookies($tokens)
    {
        if (empty($tokens['access_token']) || empty($tokens['refresh_token'])) {
            return false;
        }

        setcookie('at', $tokens['access_token'], time()+$this->accessTokenExpire, '/');
        setcookie('rt', $tokens['refresh_token'], time()+$this->refreshTokenExpire, '/');

        return true;
    }

    private function getTokensFromCookie()
    {
        try {
            $request = $this->container->get('request');
            $cookies = $request->getCookieParams();
        } catch (ContainerExceptionInterface $e) {
            $result = [];
        }

        $result['access_token'] = !empty($cookies['at']) ? $cookies['at'] : null;
        $result['refresh_token'] = !empty($cookies['rt']) ? $cookies['rt'] : null;

        return $result;
    }

    private function clearCookies_OLD()
    {
        setcookie('at', '', time()-3600, '/');
        setcookie('rt', '', time()-3600, '/');
    }

    private function createTokens($refreshToken = null)
    {
        if ($refreshToken) {
            $refreshToken = !is_array($refreshToken) ? $this->readToken($refreshToken) : $refreshToken;
            $userIp = $refreshToken['tp'];
            $userAgent = $refreshToken['tb'];
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
            'tp' => $userIp,
            'tb' => $userAgent,
            'te' => intval(date('U') + $this->accessTokenExpire),
        ];

        $result['access_token'] = $this->createToken($tokenArray);
        $tokenArray['te'] = intval(date('U') + $this->refreshTokenExpire);
        $result['refresh_token'] = $this->createToken($tokenArray);

        return $result;
    }

    private function checkToken($token)
    {
        $userIp = $this->getIpForToken();
        $userAgent = $this->getBrowserForToken();

        if (
            empty($userIp) ||
            empty($userAgent)
        ) {
            return false;
        }

        $token = !is_array($token) ? $this->readToken($token) : $token;

        return ($token &&
            isset($token['tb']) && $token['tb'] == $this->hashString($userAgent) &&
            isset($token['tp']) && $token['tp'] == $this->hashString($userIp)
        );
    }

    private function checkTokenExpire($token)
    {
        $token = !is_array($token) ? $this->readToken($token) : $token;

        return $token && isset($token['te']) && is_numeric($token['te']) && $token['te'] < date('U');
    }

    private function updateTokenInDB($refreshToken, $clearUser = false, $createNewToken = true)
    {
        $t = new Token();

        $newToken = new TokenEntity();
        $newToken->exchangeArray([
            'token' => $refreshToken,
            'ip' => $this->getIpForToken(),
            'browser' => $this->getBrowserForToken(),
            'expire' => time() + $this->refreshTokenExpire,
            'visitor' => md5($this->getIpForToken() . $this->getBrowserForToken()),
            'end' => time(),
        ]);

        try {
            if ($updateToken) {
                $t->createOnDublicateUpdate($newToken);
            } else {
                $t->create($newToken);
            }

            return true;
        } catch (\Exception $e) {
            \App\Core\Libs\Logger::log($e->getMessage());

            return false;
        }
    }

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

    private function getIpForToken()
    {
        $ip = $this->container->request->getServerParam('REMOTE_ADDR');
        if (!$ip) {
            return false;
        }

        return $ip;
    }

    private function fraudAttempt_OLD($token)
    {
        //$token = $this->readToken($token);
        $this->clearCookies();
        // clearTokenFromDB
        //clearHistory
        unset($_SESSION['rt']);
        unset($_SESSION['at']);
    }

    private function hashString($string)
    {
        return hash('sha256', $string);
    }
    */
}