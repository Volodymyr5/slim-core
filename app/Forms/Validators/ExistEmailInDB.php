<?php

namespace App\Forms\Validators;

use App\MVC\Models\User;
use Zend\Validator\AbstractValidator;

/**
 * Class ExistEmailInDB
 * @package App\Forms\Validators
 */
class ExistEmailInDB extends AbstractValidator
{
    const INVALID = "invalid";

    protected $messageTemplates = array(
        self::INVALID => "This Email not found on our site!"
    );

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $u = new User($this->getOption('container'));

        $this->setValue($value);

        $isValid = true;

        $users = $u->getAll([
            'email' => $value
        ]);

        if (count($users) == 0) {
            $this->error(self::INVALID);
            $isValid = false;
        }

        return $isValid;
    }
}
