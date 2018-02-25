<?php

namespace App\MVC\Entity;

use App\Core\EntityInterface;

class UserEntity implements EntityInterface {

    protected $id;
    protected $email;
    protected $password;
    protected $password_token;
    protected $password_token_type;
    protected $created;
    protected $updated;
    protected $first_name;
    protected $last_name;

    /**
     * $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function toArray()
    {

    }

    public function exchangeArray()
    {

    }
}


