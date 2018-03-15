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
        $params['token'] = !empty($params['token']) ? $params['token'] : null;
        $params['ip'] = !empty($params['ip']) ? $params['ip'] : null;
        $params['browser'] = !empty($params['browser']) ? $params['browser'] : null;
        $params['expire'] = !empty($params['expire']) ? $params['expire'] : null;
        $params['limit'] = !empty($params['limit']) ? $params['limit'] : null;
        $params['sort'] = !empty($params['sort']) ? $params['sort'] : null;
        $params['order'] = !empty($params['order']) ? $params['order'] : null;

        $query = $this->getQuery();

        if ($params['token']) {
            $query->where('token', $params['token']);
        }

        if ($params['ip']) {
            $query->where('ip', $params['ip']);
        }

        if ($params['browser']) {
            $query->where('browser', $params['browser']);
        }

        if ($params['expire']) {
            $params['expire'] = is_numeric($params['expire']) ? date('Y-m-d H:i:s', ($params['expire'] - 960)) : $params['expire'];

            $query->where_gt('expire', $params['expire']);
        }

        if ($params['limit']) {
            $query->limit($params['limit']);
        }

        if ($params['sort'] && $params['order']) {
            if ($params['order'] == 'asc') {
                $query->orderByAsc($params['sort']);
            } elseif ($params['order'] == 'desc') {
                $query->orderByDesc($params['sort']);
            }
        }

        $result = $query->findMany();

        print_r($query->get_last_query());

        return $result;
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

