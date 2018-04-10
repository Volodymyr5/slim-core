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
        $ignoreSelf = $this->getOption('ignore_self', null);
        if ($ignoreSelf) {
            $id = $this->getOption('container')->get('request')->getParam('id', null);
            $id = (is_numeric($id) && $id > 0) ? $id : null;

            $u = new User($this->getOption('container'));
            $user = $id ? $u->getByField('id', $id) : null;
            $user = isset($user['id']) ? $user : null;
        }

        $this->setValue($value);

        $isValid = true;

        if ($ignoreSelf && $user['email'] == $value) {
            return $isValid;
        }

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
