<?php

namespace App\MVC\Entity;

use App\Core\EntityInterface;

/**
 * Class TokenEntity
 * @package App\MVC\Entity
 */
class TokenEntity implements EntityInterface {

    protected $id;
    protected $token;
    protected $ip;
    protected $browser;
    protected $expire;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @param $browser
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    /**
     * @return mixed
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * @param $expire
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $dirtyData = [
            'id' => $this->getId(),
            'token' => $this->getToken(),
            'ip' => $this->getIp(),
            'browser' => $this->getBrowser(),
            'expire' => $this->getExpire(),
        ];

        $data = [];
        foreach ($dirtyData as $name => $val) {
            if (!is_null($val)) {
                $data[$name] = $val;
            }
        }

        return $data;
    }

    /**
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        $this->setId((isset($data['id']) ? $data['id'] : null));
        $this->setToken((isset($data['token']) ? $data['token'] : null));
        $this->setIp((isset($data['ip']) ? $data['ip'] : null));
        $this->setBrowser((isset($data['browser']) ? $data['browser'] : null));
        $this->setExpire((isset($data['expire']) ? $data['expire'] : null));
    }
}