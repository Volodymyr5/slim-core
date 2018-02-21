<?php

namespace App\Core\Libs;

/**
 * Class JwtAuth
 * @package App\Core\Libs
 */
class JwtAuth
{
    public function __construct(){}

    public function isLogged()
    {

    }

    public function login($userId)
    {

    }

    public function logout($withRedirect = true)
    {

    }

    private function setCookie($userId, $accessToken, $refreshToken)
    {

    }

    private function createToken($tokenArray)
    {

    }

    private function getBrowserForToken()
    {

    }

    private function getIpForToken()
    {

    }
//    public function getUser($strict = false)
//    public function reworkTokens()
//    public function reworkClientTokens($clientTokens, $strictRefreshToken = false)
//    public function syncClientToken()
//    public function checkClientTokens($refreshToken = false, $immortalToken = false, $params = array())
//    private function createAllTokens($userId)
//    private function createAllTokensForClient($tokenData, $params = array())
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