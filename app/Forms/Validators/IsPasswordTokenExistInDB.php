<?php

namespace App\Forms\Validators;

use App\MVC\Models\User;
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
        $u = new User();

        $this->setValue($value);

        $isValid = true;

        $users = $u->getAll([
            'password_token' => $value
        ]);

        if (count($users)) {
            $this->error(self::INVALID);
            $isValid = false;
        }

        return $isValid;
    }
}
