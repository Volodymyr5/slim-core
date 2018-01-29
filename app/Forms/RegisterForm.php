<?php

namespace App\Forms;

use App\Forms\Validators\CustomAlpha;
use \Zend\Validator\Callback;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;

/**
 * Class RegisterForm
 * @package App\Forms
 */
class RegisterForm extends Form implements InputFilterProviderInterface
{
    public function init()
    {
        $formName = $this->getName() ? $this->getName() . '-' : '';

        // First name
        $firstName = new Element\Text('first_name');
        $firstName->setLabelAttributes([
            'for' => $formName . $firstName->getAttribute('name'),
        ]);
        $firstName->setAttributes([
            'id' => $formName . $firstName->getAttribute('name'),
            'class' => 'uk-input',
            //'required' => 'required',
            'placeholder' => 'First name',
        ]);

        // Last name
        $lastName = new Element\Text('last_name');
        $lastName->setLabelAttributes([
            'for' => $formName . $lastName->getAttribute('name'),
        ]);
        $lastName->setAttributes([
            'id' => $formName . $lastName->getAttribute('name'),
            'class' => 'uk-input',
            'placeholder' => 'Last name',
        ]);

        // Email
        $email = new Element\Email('email');
        $email->setLabelAttributes([
            'for' => $formName . $email->getAttribute('name'),
        ]);
        $email->setAttributes([
            'id' => $formName . $email->getAttribute('name'),
            'class' => 'uk-input',
            //'required' => 'required',
            'placeholder' => 'Email',
        ]);

        // Password
        $password = new Element\Password('password');
        $password->setLabelAttributes([
            'for' => $formName . $password->getAttribute('name'),
        ]);
        $password->setAttributes([
            'id' => $formName . $password->getAttribute('name'),
            'class' => 'uk-input',
            //'required' => 'required',
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
            //'required' => 'required',
            'placeholder' => 'Repeat password',
        ]);

        // Submit button
        $submit = new Element\Submit('submit');
        $submit->setAttributes([
            'id' => $formName . $submit->getAttribute('name'),
            'value' => 'Sign Up',
            'class' => 'uk-button uk-button-primary',
            'type' => 'submit'
        ]);

        // Add elements to form
        $this->add($firstName);
        $this->add($lastName);
        $this->add($email);
        $this->add($password);
        $this->add($repassword);
        $this->add($submit);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        // First Name
        $firstName = [
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
                        'min' => 2,
                        'max' => 255,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Minimum length - %min% characters',
                            StringLength::TOO_LONG => 'Maximum length - %max% characters',
                        ]
                    ]
                ],
                new CustomAlpha([
                    'messages' => [
                        CustomAlpha::INVALID => 'The field must contains only characters',
                    ]
                ])
            ],
        ];

        // Last Name
        $lastName = $firstName;
        $lastName['required'] = false;

        // Email
        $email = [
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
                ['name' => 'StripNewLines'],
            ],
            'validators' => [
                ['name' => 'EmailAddress'],
                ['name' => '\App\Forms\Validators\UniqueEmailInDB'],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 128,
                        'messages' => [
                            StringLength::TOO_LONG => 'Maximum length - %max% characters',
                        ]
                    ]
                ]
            ]
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
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
            'repassword' => $repassword,
        ];
    }
}
