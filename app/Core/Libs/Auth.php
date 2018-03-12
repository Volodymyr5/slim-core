<?php

namespace App\Core\Libs;
use App\MVC\Entity\TokenEntity;
use App\MVC\Models\Token;
use Firebase\JWT\JWT;
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

    protected $accessTokenExpire;

    protected $refreshTokenExpire;

    /**
     * @param ContainerInterface $container
     * @throws \Exception
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $config = isset($container->settings['custom']) ? $this->container->settings['custom'] : [];

        $this->publicKey = !empty($config['jwt']['public']) ? $config['jwt']['public'] : null;
        $this->privateKey = !empty($config['jwt']['private']) ? $config['jwt']['private'] : null;
        $this->accessTokenExpire = !empty($config['jwt']['access_token']) ? $config['jwt']['access_token'] : null;
        $this->refreshTokenExpire = !empty($config['jwt']['refresh_token']) ? $config['jwt']['refresh_token'] : null;

        if (empty($this->publicKey) || empty($this->privateKey)) {
            throw new \Exception('Error. Public or private token not set in local.php!');
        }

        if (empty($this->accessTokenExpire) || empty($this->refreshTokenExpire)) {
            throw new \Exception('Error. Access token Expire or Refresh token Expire token not set in local.php!');
        }
    }

    public function isLogged()
    {

    }

    public function update()
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
                $this->setCookies($tokens);
            }
        } else {
            $tokens = $this->createTokens();
            if (!$tokens) {
                return false;
            }
            $this->setCookies($tokens);
        }

        if (empty($tokens['access_token']) || empty($tokens['refresh_token'])) {
            return false;
        }
        $this->updateTokenInDB($tokens['refresh_token']);
        $this->setCookies($tokens);
    }

    /**
     * @param $tokens
     * @return bool
     */
    private function setCookies($tokens)
    {
        if (empty($tokens['access_token']) || empty($tokens['refresh_token'])) {
            return false;
        }

        setcookie('at', $tokens['access_token'], time()+$this->accessTokenExpire, '/');
        setcookie('rt', $tokens['refresh_token'], time()+$this->refreshTokenExpire, '/');

        return true;
    }

    /**
     * @return array
     */
    private function getTokensFromCookie()
    {
        $request = $this->container->get('request');
        $cookies = $request->getCookieParams();

        $result['access_token'] = !empty($cookies['at']) ? $cookies['at'] : null;
        $result['refresh_token'] = !empty($cookies['rt']) ? $cookies['rt'] : null;

        return $result;
    }

    /**
     *
     */
    private function clearCookies()
    {
        setcookie('at', '', time()-3600, '/');
        setcookie('rt', '', time()-3600, '/');
    }

    /**
     * @param null $refreshToken
     * @return array|bool
     */
    private function createTokens($refreshToken = null)
    {
        if ($refreshToken) {
            $refreshToken = is_array($refreshToken) ? $this->readToken($refreshToken) : $refreshToken;
            $userIp = $refreshToken['tp'];
            $userAgent = $refreshToken['tb'];
        } else {
            $userIp = $this->getIpForToken();
            $userAgent = $this->getBrowserForToken();
        }

        if (
            empty($userIp) ||
            empty($userAgent)
        ) {
            return false;
        }

        $tokenArray = [
            'tp' => hash('sha256', $userIp),
            'tb' => hash('sha256', $userAgent),
            'te' => intval(date('U') + $this->accessTokenExpire),
        ];

        $result['access_token'] = $this->createToken($tokenArray);
        $tokenArray['te'] = intval(date('U') + $this->refreshTokenExpire);
        $result['refresh_token'] = $this->createToken($tokenArray);

        return $result;
    }

    /**
     * @param $token
     * @return bool
     */
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

        $token = is_array($token) ? $this->readToken($token) : $token;

        return ($token &&
            isset($token['tb']) && $token['tb'] == hash('sha256', $userIp) &&
            isset($token['tp']) && $token['tp'] == hash('sha256', $userAgent)
        );
    }

    /**
     * @param $token
     * @return bool
     */
    private function checkTokenExpire($token)
    {
        $token = is_array($token) ? $this->readToken($token) : $token;

        return $token && isset($token['te']) && is_numeric($token['te']) && $token['te'] < date('U');
    }

    /**
     * @param $tokenArray
     * @return string|boolean
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
    private function readToken($token)
    {
        try {
            $tokenArray = JWT::decode($token, $this->publicKey, array('RS256'));
            $tokenArray = (array) $tokenArray;
            return $tokenArray;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $refreshToken
     */
    private function updateTokenInDB($refreshToken)
    {
        $t = new Token();

        $newToken = new TokenEntity();
        $newToken->exchangeArray([
            'token' => $refreshToken,
            'ip' => $this->getIpForToken(),
            'browser' => $this->getBrowserForToken(),
            'expire' => $this->refreshTokenExpire,
        ]);

        $t->createOnDublicateUpdate($refreshToken);
    }

    /**
     * @return bool|string
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
     * @return bool|string
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
        $this->clearCookies();
        // clearTokenFromDB
        //clearHistory
        unset($_SESSION['rt']);
        unset($_SESSION['at']);

        $this->update();
    }


//    public function getUser($strict = false)
//    public function reworkTokens()
//    private function createAllTokens($userId)
//    private function getTokensFromCookie()
//    private function checkTokenParams($token, $checkExpire = true)
//    private function checkRefreshToken($refreshTokenData)
//    public function readToken($token)
//    private function logoutRedirect()
//    private function updateUserRefreshTokenList($userId, $refreshToken)
//    private function deleteCurrentRefreshToken($tokenData)
//    public function getFirstRefreshTokenFromDb($immortalToken, $strict = false)
//    private function getRefreshTokensFromDb($userId, $browser, $ip)
}