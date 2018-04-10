<?php

namespace App\Forms\Validators;

use App\MVC\Models\User;
use Zend\Validator\AbstractValidator;

/**
 * Class IsRole
 * @package App\Forms\Validators
 */
class IsRole extends AbstractValidator
{
    const INVALID = "invalid";

    protected $messageTemplates = array(
        self::INVALID => "Value is not valid Role!"
    );

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $container = $this->getOption('container');
        $roles = $container->acl->getRoles();
        $roles = array_flip($roles);

        $isValid = true;

        if (!isset($roles[$value])) {
            $this->error(self::INVALID);
            $isValid = false;
        }

        return $isValid;
    }
}
