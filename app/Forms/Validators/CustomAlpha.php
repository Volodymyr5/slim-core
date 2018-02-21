<?php

namespace App\Forms\Validators;

use Zend\Validator\AbstractValidator;

/**
 * Class CustomAlpha
 * @package App\Forms\Validators
 */
class CustomAlpha extends AbstractValidator
{
    const INVALID = "invalid";

    protected $messageTemplates = array(
        self::INVALID => "Field must contains only characters"
    );

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $isValid = true;

        if (!preg_match("/^[a-zA-Zа-яА-Я- ІіЇїҐґЁё]+$/usi", $value)) {
            $this->error(self::INVALID);
            $isValid = false;
        }

        return $isValid;
    }
}
