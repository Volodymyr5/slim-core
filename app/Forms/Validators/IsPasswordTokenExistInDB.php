<?php

namespace App\Forms\Validators;

use Zend\Validator\AbstractValidator;

/**
 * Class IsPasswordTokenExistInDB
 * @package App\Forms\Validators
 */
class IsPasswordTokenExistInDB extends AbstractValidator
{
    const INVALID = "invalid";

    protected $messageTemplates = array(
        self::INVALID => "Token not found!"
    );

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $isValid = true;

        $users = \Model::factory('\App\Models\User')
            ->where('password_token', $value)
            ->find_many();

        if (count($users)) {
            $this->error(self::INVALID);
            $isValid = false;
        }

        return $isValid;
    }
}
