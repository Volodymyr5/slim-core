<?php

namespace App\Forms\Validators;

use Zend\Validator\AbstractValidator;

/**
 * Class UniqueEmailInDB
 * @package App\Forms\Validators
 */
class UniqueEmailInDB extends AbstractValidator
{
    const INVALID = "invalid";

    protected $messageTemplates = array(
        self::INVALID => "Email is already taken!"
    );

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $isValid = true;

        $users = \Model::factory('\App\MVC\Models\User')
            ->where('email', $value)
            ->find_many();

        if (count($users)) {
            $this->error(self::INVALID);
            $isValid = false;
        }

        return $isValid;
    }
}
