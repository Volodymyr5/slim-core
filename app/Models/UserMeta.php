<?php

namespace App\Models;

/**
 * Class UserMeta
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $register_confirm_token
 * @property string $forgot_password_token
 */
class UserMeta extends CoreModel {

    /**
     * @return $this|null
     */
    public function user() {
        return $this->belongs_to('\App\Models\User');
    }
}
