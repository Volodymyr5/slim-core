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
        $options = $this->getOptions();
        $ignoreSelf = isset($options['ignore_self']) ? $options['ignore_self'] : null;
        $container = isset($options['container']) ? $options['container'] : null;

        $u = new User($container);

        if ($ignoreSelf) {
            $id = $container->get('request')->getParam('id', null);
            $id = (is_numeric($id) && $id > 0) ? $id : null;

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
