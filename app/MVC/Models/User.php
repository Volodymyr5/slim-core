<?php

namespace App\MVC\Models;

use App\Core\CoreModel;

/**
 * Class User
 * @package App\MVC\Models
 *
 * @property string id
 * @property string email
 * @property string password
 * @property string password_token
 * @property string password_token_type
 * @property string created
 * @property string updated
 */
class User extends CoreModel {

    /**
     * @return \ORMWrapper
     */
    public function userMeta() {
        return $this->has_one('\App\MVC\Models\UserMeta');
    }
}
