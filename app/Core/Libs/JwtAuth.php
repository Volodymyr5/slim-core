<?php

namespace App\Core\Libs;
use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;

/**
 * Class JwtAuth
 * @package App\Core\Libs
 */
class JwtAuth
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

        if (empty($this->accessTokenExpire) || empty($this->refreshTokenTExpire)) {
            throw new \Exception('Error. Access token Expire or Refresh token Expire token not set in local.php!');
        }
    }

    public function isLogged()
    {
        var_dump(
            $this->getBrowserForToken(),
            $this->getIpForToken()
        );
    }

    /**
     * @param $userId
     */
    public function login($userId)
    {
        $tokens = $this->createTokens($userId);
        $this->setTokens($tokens);
        $this->setTokens($tokens);
        $this->setTokens($tokens);
    }

    public function logout($withRedirect = true)
    {

    }

    private function setCookie($userId, $accessToken, $refreshToken)
    {

    }

    /**
     * @param $userId
     * @return bool|array
     */
    private function createTokens($userId)
    {
        $userId = (is_numeric($userId) && $userId > 0) ? $userId : null;

        $userIp = $this->getIpForToken();
        $userAgent = $this->getBrowserForToken();

        if (
            empty($userId) ||
            empty($userIp) ||
            empty($userAgent)
        ) {
            return false;
        }

        $tokenArray = [
            'td' => hash('sha256', $userId),
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