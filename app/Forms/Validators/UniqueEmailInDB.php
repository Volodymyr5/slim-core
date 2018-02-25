<?php

namespace App\Forms\Validators;

use App\MVC\Models\User;
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
        $u = new User();

        $this->setValue($value);

        $isValid = true;

        $users = $u->getAll([
            'email' => $value
        ]);

        if (count($users)) {
            $this->error(self::INVALID);
            $isValid = false;
        }

        return $isValid;
    }
}
