<?php

namespace App\MVC\Models;

use App\Core\CoreModel;
use App\MVC\Entity\UserEntity;

/**
 * Class User
 * @package App\MVC\Models
 */
class User extends CoreModel {

    const TABLE = 'user';
    const USER_META_TABLE = 'user_meta';

    const COLUMNS = [
        self::TABLE . '.id',
        self::TABLE . '.role',
        self::TABLE . '.email',
        self::TABLE . '.password',
        self::TABLE . '.password_token',
        self::TABLE . '.token_expiration',
        self::TABLE . '.created',
        self::TABLE . '.updated',
    ];

    const META_COLUMNS = [
        self::USER_META_TABLE . '.first_name',
        self::USER_META_TABLE . '.last_name',
    ];

    /**
     * @param array $params
     * @return array
     */
    public function getAll($params = [])
    {
        $params['ids'] = isset($params['ids']) && is_array($params['ids']) ? $params['ids'] : array();
        $params['email'] = isset($params['email']) ? $params['email'] : null;
        $params['password'] = isset($params['password']) ? $params['password'] : null;
        $params['password_token'] = isset($params['password_token']) ? $params['password_token'] : null;
        $params['first_name'] = isset($params['first_name']) ? $params['first_name'] : null;
        $params['last_name'] = isset($params['last_name']) ? $params['last_name'] : null;

        $query = $this->getQuery();

        if (!empty($params['ids'])) {
            $query->where(self::TABLE . '.id', $params['ids']);
        }

        if ($params['email']) {
            $query->where(self::TABLE . '.email like ?', "%{$params['email']}%");
        }

        if ($params['password']) {
            $query->where(self::TABLE . '.password', $params['password']);
        }

        if ($params['password_token']) {
            $query->where(self::TABLE . '.password_token', $params['password_token']);
        }

        if ($params['first_name']) {
            $query->where(self::USER_META_TABLE . '.first_name like ?', "%{$params['first_name']}%");
        }

        if ($params['last_name']) {
            $query->where(self::USER_META_TABLE . '.last_name like ?', "%{$params['last_name']}%");
        }

        return $this->extract($query);
    }

    /**
     * @param $name
     * @param $value
     * @return array
     */
    public function getByField($name, $value)
    {
        $name = $name == 'id' ? self::TABLE . '.id' : $name;

        $query = $this->getQuery();

        $query->where($name, $value);

        return $this->extract($query, true);
    }

    /**
     * @param UserEntity $e
     * @param bool $updateMeta
     * @return int
     * @throws \Exception
     */
    public function create(UserEntity $e, $updateMeta = false)
    {
        try {
            $data = $e->toArray();

            $mainData = [];
            foreach ($data as $row => $value) {
                $rowFullName = self::TABLE . ".{$row}";
                if (in_array($rowFullName, self::COLUMNS)) {
                    $mainData[$row] = $value;
                }
            }

            $id = $this->db->insertInto(self::TABLE, $mainData)->execute();

            if ($updateMeta) {
                $metaData = [];
                foreach ($data as $row => $value) {
                    $rowFullName = self::USER_META_TABLE . ".{$row}";
                    if (in_array($rowFullName, self::META_COLUMNS)) {
                        $metaData[$row] = $value;
                    }
                }

                if (!empty($metaData['first_name'])) {
                    $metaData['user_id'] = $id;

                    $metaId = $this->db->insertInto(self::USER_META_TABLE, $metaData)->execute();
                }
            }

            return $id;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param UserEntity $e
     * @param bool $updateMeta
     * @return int
     * @throws \Exception
     */
    public function modify(UserEntity $e, $updateMeta = false)
    {
        try {
            $data = $e->toArray();

            $mainData = [];
            foreach ($data as $row => $value) {
                $rowFullName = self::TABLE . ".{$row}";
                if (in_array($rowFullName, self::COLUMNS)) {
                    $mainData[$row] = $value;
                }
            }

            $id = $this->db->update(self::TABLE, $mainData, $e->getId())->execute();

            if ($updateMeta) {
                $metaData = [];
                foreach ($data as $row => $value) {
                    $rowFullName = self::USER_META_TABLE . ".{$row}";
                    if (in_array($rowFullName, self::META_COLUMNS)) {
                        $metaData[$row] = $value;
                    }
                }

                if (!empty($metaData['first_name'])) {
                    $metaId = $this->db->update(self::USER_META_TABLE)->set($metaData)->where('user_id', $e->getId())->execute();
                }
            }

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
        $columns = array_merge(self::COLUMNS, self::META_COLUMNS);

        $query = $this->db->from(self::TABLE);

        $query->leftJoin(self::USER_META_TABLE . ':');

        $query->select($columns);

        return $query;
    }
}

