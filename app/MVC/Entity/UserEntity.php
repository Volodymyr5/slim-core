<?php

namespace App\MVC\Entity;

use App\Core\EntityInterface;

/**
 * Class UserEntity
 * @package App\MVC\Entity\UserEntity
 */
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
     * @return $id
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
     * @return $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return $password_token
     */
    public function getPasswordToken()
    {
        return $this->password_token;
    }

    /**
     * @param $password_token
     */
    public function setPasswordToken($password_token)
    {
        $this->password_token = $password_token;
    }

    /**
     * @return $password_token_type
     */
    public function getPasswordTokenType()
    {
        return $this->password_token_type;
    }

    /**
     * @param $password_token_type
     */
    public function setPasswordTokenType($password_token_type)
    {
        $this->password_token_type = $password_token_type;
    }

    /**
     * @return $created
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return $updated
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return $first_name
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return $last_name
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return[
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'password_token' => $this->getPasswordToken(),
            'password_token_type' => $this->getPasswordTokenType(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
        ];
    }

    /**
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        $this->setId((!empty($data['id']) ? $data['id'] : null));
        $this->setEmail((!empty($data['email']) ? $data['email'] : null));
        $this->setpassword((!empty($data['password']) ? $data['password'] : null));
        $this->setPasswordToken((!empty($data['password_token']) ? $data['password_token'] : null));
        $this->setPasswordTokenType((!empty($data['password_token_type']) ? $data['password_token_type'] : null));
        $this->setCreated((!empty($data['created']) ? $data['created'] : null));
        $this->setUpdated((!empty($data['updated']) ? $data['updated'] : null));
        $this->setFirstName((!empty($data['first_name']) ? $data['first_name'] : null));
        $this->setLastName((!empty($data['last_name']) ? $data['last_name'] : null));
    }
}