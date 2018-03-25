<?php

namespace App\Forms;

use \Zend\Validator\Callback;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;

/**
 * Class SetPasswordForm
 * @package App\Forms
 */
class SetPasswordForm extends Form implements InputFilterProviderInterface
{
    public function init()
    {
        $formName = $this->getName() ? $this->getName() . '-' : '';

        // Token
        $token = new Element\Hidden('token');

        // Password
        $password = new Element\Password('password');
        $password->setLabelAttributes([
            'for' => $formName . $password->getAttribute('name'),
        ]);
        $password->setAttributes([
            'id' => $formName . $password->getAttribute('name'),
            'class' => 'uk-input',
            'required' => 'required',
            'placeholder' => 'Password',
        ]);

        // Repeat password
        $repassword = new Element\Password('repassword');
        $repassword->setLabelAttributes([
            'for' => $formName . $repassword->getAttribute('name'),
        ]);
        $repassword->setAttributes([
            'id' => $formName . $repassword->getAttribute('name'),
            'class' => 'uk-input',
            'required' => 'required',
            'placeholder' => 'Repeat password',
        ]);

        // Submit button
        $submit = new Element\Submit('submit');
        $submit->setAttributes([
            'id' => $formName . $submit->getAttribute('name'),
            'value' => 'Submit',
            'class' => 'uk-button uk-button-primary',
            'type' => 'submit'
        ]);

        // Add elements to form
        $this->add($token);
        $this->add($password);
        $this->add($repassword);
        $this->add($submit);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        // Token
        $token = [
            'required' => true,
            'validators' => [
                [
                    'name' => '\App\Forms\Validators\IsPasswordTokenValid',
                    'options' => [
                        'container' => $this->getOption('container'),
                    ]
                ],
            ],
        ];

        // Password
        $password = [
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
                ['name' => 'StripNewLines'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 7,
                        'max' => 40,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Minimum password length - %min% characters',
                            StringLength::TOO_LONG => 'Maximum password length - %max% characters',
                        ]
                    ]
                ],
                [
                    'name' => 'Callback',
                    'options' => [
                        'callback' => function ($value, $context = []) {
                            $is_secure = true;

                            if (!preg_match("#[0-9]+#", $value)) {
                                $is_secure = false;
                            }

                            if (!preg_match("#[a-z]+#", $value)) {
                                $is_secure = false;
                            }

                            if (!preg_match("#[A-Z]+#", $value)) {
                                $is_secure = false;
                            }

                            if (!preg_match("#\W+|_#", $value)) {
                                $is_secure = false;
                            }

                            return $is_secure;
                        },
                        'messages' => [
                            Callback::INVALID_VALUE => 'Password is not secure enough. It should contain numbers, letters in upper and lower case and at least one special symbol.',
                        ],
                    ],
                ],
            ],
        ];

        // Repeat Password
        $repassword = [
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
                ['name' => 'StripNewLines'],
            ],
            'validators' => [
                [
                    'name' => 'Identical',
                    'options' => [
                        'token' => 'password',
                        'messages' => [
                            Identical::NOT_SAME => 'Passwords must match',
                        ]
                    ]
                ]
            ]
        ];

        return [
            'token' => $token,
            'password' => $password,
            'repassword' => $repassword,
        ];
    }
}
