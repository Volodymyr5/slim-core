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
            'options' => [
                'label' => 'First name',
            ],
            'attributes' => [
                'id' => 'first_name',
                'class' => 'uk-input',
                'required' => 'required',
            ],
        ]);
        // Last name
        $this->add([
            'name' => 'last_name',
            'type' => 'text',
            'options' => [
                'label' => 'Last name',
            ],
            'attributes' => [
                'id' => 'last_name',
                'class' => 'uk-input',
            ],
        ]);
        // Email
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email',
            ],
            'attributes' => [
                'id' => 'email',
                'class' => 'uk-input',
                'required' => 'required',
            ],
        ]);
        // Password
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'id' => 'password',
                'class' => 'uk-input',
                'required' => 'required',
            ],
        ]);
        // Repeat password
        $this->add([
            'name' => 'repassword',
            'type' => 'repassword',
            'options' => [
                'label' => 'Repeat password',
            ],
            'attributes' => [
                'id' => 'repassword',
                'class' => 'uk-input',
                'required' => 'required',
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
                'class' => 'uk-button uk-button-default',
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
