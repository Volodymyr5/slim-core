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
        $params['order'] = !empty($params['order']) && in_array($params['order'], ['asc', 'desc']) ? $params['order'] : null;

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
            $query->where('expire > ?', $params['expire']);
        }

        if ($params['limit']) {
            $query->limit($params['limit']);
        }

        if ($params['sort'] && $params['order']) {
            $params['order'] = strtoupper($params['order']);
            $query->orderBy("{$params['sort']} {$params['order']}");
        }

        return $this->extract($query);
    }

    /**
     * @param $token
     * @return array
     */
    public function getByToken($token)
    {
        $query = $this->getQuery();
        $query->where('token', $token);

        $result = $this->extract($query, true);

        return $result;
    }

    /**
     * @param TokenEntity $e
     * @return array|mixed|null
     * @throws \Exception
     */
    public function create(TokenEntity $e)
    {
        try {
            $data = $e->toArray();

            $id = $this->db->insertInto(self::TABLE, $data)->execute();

            return $id;
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

            $id = $this->db->update(self::TABLE, $data, $e->getId())->execute();

            return $id;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return \BaseQuery
     */
    private function getQuery ()
    {
        $columns = [
            'id',
            'user_id',
            'visitor',
            'token',
            'ip',
            'browser',
            'start',
            'end',
            'expire',
        ];

        $query = $this->db->from(self::TABLE);
        $query->select($columns);

        return $query;
    }
}

