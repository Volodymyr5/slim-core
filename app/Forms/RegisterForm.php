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
    /**
     * Init form
     */
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
            'required' => 'required',
            'autofocus' => 'autofocus',
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

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ];
    }
}
