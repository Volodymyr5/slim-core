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

    /**
     * @param array $params
     * @return array
     */
    public function getAll($params = [])
    {
        $params['email'] = isset($params['email']) ? $params['email'] : null;
        $params['password_token'] = isset($params['password_token']) ? $params['password_token'] : null;

        $query = $this->getQuery();

        if ($params['email']) {
            $query->where('email', $params['email']);
        }

        if ($params['password_token']) {
            $query->where('password_token', $params['password_token']);
        }

        return  $query->findArray();
    }

    /**
     * @param UserEntity $e
     * @return array|mixed|null
     * @throws \Exception
     */
    public function create(UserEntity $e)
    {
        try {
            $newUser = \ORM::forTable(self::TABLE)->create();
            $newUser->email = $e->getEmail();
            $newUser->password = $e->getPassword();
            $newUser->password_token = $e->getPasswordToken();
            $newUser->token_expiration = $e->getTokenExpiration();
            $newUser->save();
            $newUserId = $newUser->id;

            if (
                $e->getFirstName() ||
                $e->getLastName()
            ) {
                $newUserMeta = \ORM::forTable(self::USER_META_TABLE)->create();
                $newUserMeta->user_id = $newUserId;
                $newUserMeta->first_name = $e->getFirstName();
                $newUserMeta->last_name = $e->getLastName();
                $newUserMeta->save();
            }

            return $newUserId;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param UserEntity $e
     * @throws \Exception
     */
    public function modify(UserEntity $e)
    {
        try {
            $data = $e->toArray();

            $user = \ORM::forTable(self::TABLE)->findOne($e->getId());

            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $user->set($data);
            $user->save();

            if (
                isset($data['first_name']) ||
                isset($data['last_name'])
            ) {
                $userMeta = \ORM::forTable(self::USER_META_TABLE)->where('user_id', $e->getId())->findOne();
                $userMeta->first_name = $e->getFirstName();
                $userMeta->last_name = $e->getLastName();
                $userMeta->save();
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function getQuery ()
    {
        $query = \ORM::forTable(self::TABLE);

        return $query;
    }
}

