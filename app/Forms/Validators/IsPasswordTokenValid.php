<?php

namespace App\Forms\Validators;

use App\MVC\Models\User;
use Zend\Validator\AbstractValidator;

/**
 * Class IsPasswordTokenValid
 * @package App\Forms\Validators
 */
class IsPasswordTokenValid extends AbstractValidator
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

        if (!preg_match('/^[a-zA-Z0-9]{10,45}$/ui', $value)) {
            $this->error('Invalid token syntax!');
            return false;
        }

        $users = $u->getAll([
            'password_token' => $value
        ]);

        if (count($users) <= 0) {
            $this->error(self::INVALID);
            $isValid = false;
        }

        return $isValid;
    }
}
