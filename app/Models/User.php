<?php

namespace App\Models;

/**
 * Class User
 * @package App\Models
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $access_token
 * @property string $update_token
 * @property int $created
 * @property int $updated
 */
class User extends CoreModel {

    /**
     * @return \ORMWrapper
     */
    public function userMeta() {
        return $this->has_one('\App\Models\UserMeta');
    }
}
