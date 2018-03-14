<?php

namespace App\MVC\Models;

use App\Core\CoreModel;
use App\MVC\Entity\TokenEntity;

/**
 * Class Token
 * @package App\MVC\Models
 */
class Token extends CoreModel {

    const TABLE = 'token';

    /**
     * @param array $params
     * @return array
     */
    public function getAll($params = [])
    {
        $query = $this->getQuery();

        return  $query->findArray();
    }

    /**
     * @param $token
     * @return array
     */
    public function getByToken($token)
    {
        $query = $this->getQuery();
        $result = $query->where('token', $token)->findOne();

        return (array)$result;
    }

    /**
     * @param $ip
     * @param $browser
     * @return null
     */
    public function getByIpBrowser($ip, $browser, $checkExpiration = null)
    {
        $query = $this->getQuery();
        $query->where([
            'ip' => $ip,
            'browser' => $browser
        ])
        ->orderByDesc('end')
        ->limit(1);

        if ($checkExpiration) {
            $query->where_gt('expire', time());
        }

        $result = $query->findArray();

        return !empty($result[0]['id']) ? $result[0] : null;
    }

    /**
     * @param TokenEntity $e
     * @throws \Exception
     */
    public function createOnDublicateUpdate(TokenEntity $e)
    {
        $token = $this->getByIpBrowser($e->getIp(), $e->getBrowser());

        if (!empty($token['id'])) {
            $e->setId($token['id']);
            $this->modify($e);
        } else {
            $this->create($e);
        }
    }

    /**
     * @param TokenEntity $e
     * @return array|mixed|null
     * @throws \Exception
     */
    public function create(TokenEntity $e)
    {
        try {
            $tokenData = $e->toArray();

            $newToken = \ORM::forTable(self::TABLE)->create();
            $newToken->set($tokenData);
            $newToken->save();
            $newTokenId = $newToken->id;

            return $newTokenId;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param TokenEntity $e
     * @throws \Exception
     */
    public function modify(TokenEntity $e)
    {
        try {
            $data = $e->toArray();

            $token = \ORM::forTable(self::TABLE)->findOne($e->getId());
            $token->set($data);
            $token->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return \ORM
     */
    protected function getQuery ()
    {
        $query = \ORM::forTable(self::TABLE);

        return $query;
    }
}

