<?php

namespace App\Forms\Validators;

use App\MVC\Models\User;
use Zend\Validator\AbstractValidator;

/**
 * Class EmailPasswordInDB
 * @package App\Forms\Validators
 */
class EmailPasswordInDB extends AbstractValidator
{
    const INVALID = "invalid";

    protected $messageTemplates = array(
        self::INVALID => "Wrong Login or Password!"
    );

    /**
     * @param mixed $value
     * @param null $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $u = new User();

        $this->setValue($value);


        if (empty($context['email'])) {
            $this->error(self::INVALID);
            return false;
        }

        $user = $u->getByEmail($context['email']);

        if (empty($user->password)) {
            $this->error(self::INVALID);
            return false;
        }

        if (password_verify($value, $user->password)) {
            return true;
        } else {
            $this->error(self::INVALID);
            return false;
        }
    }
}