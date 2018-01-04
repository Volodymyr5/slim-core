<?php

namespace App\Forms;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class RegisterForm
 * @package App\Forms
 */
class RegisterForm extends Form implements InputFilterProviderInterface
{
    public function init()
    {
        // First name
        $this->add([
            'name' => 'first_name',
            'type' => 'text',
            'attributes' => [
                'id' => 'first_name',
                'class' => 'uk-input',
                'required' => 'required',
                'placeholder' => 'First name',
            ],
        ]);
        // Last name
        $this->add([
            'name' => 'last_name',
            'type' => 'text',
            'attributes' => [
                'id' => 'last_name',
                'class' => 'uk-input',
                'placeholder' => 'Last name',
            ],
        ]);
        // Email
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'attributes' => [
                'id' => 'email',
                'class' => 'uk-input',
                'required' => 'required',
                'placeholder' => 'Email',
            ],
        ]);
        // Password
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'attributes' => [
                'id' => 'password',
                'class' => 'uk-input',
                'required' => 'required',
                'placeholder' => 'Password',
            ],
        ]);
        // Repeat password
        $this->add([
            'name' => 'repassword',
            'type' => 'password',
            'attributes' => [
                'id' => 'repassword',
                'class' => 'uk-input',
                'required' => 'required',
                'placeholder' => 'Repeat password',
            ],
        ]);

        // Submit button
        $this->add([
            'name' => 'submit',
            'type' => 'button',
            'options' => [
                'label' => 'Sign Up',
            ],
            'attributes' => [
                'class' => 'uk-button uk-button-primary',
                'type'  => 'submit',
            ],
        ]);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'first_name' => [
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
                'validators' => [
                    ['name' => 'EmailAddress'],
                ],
            ],
            'email' => [
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
                'validators' => [
                    ['name' => 'EmailAddress'],
                ],
            ],
        ];
    }
}
