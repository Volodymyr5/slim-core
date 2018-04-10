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
 * Class EditUserForm
 * @package App\Forms
 */
class EditUserForm extends Form implements InputFilterProviderInterface
{
    /**
     * Init form
     */
    public function init()
    {
        $formName = $this->getName() ? $this->getName() . '-' : '';

        // Id
        $id = new Element\Hidden('id');

        // Email
        $email = new Element\Email('email');
        $email->setLabelAttributes([
            'for' => $formName . $email->getAttribute('name'),
        ]);
        $email->setAttributes([
            'id' => $formName . $email->getAttribute('name'),
            'class' => 'uk-input',
            'placeholder' => 'Email',
            'readonly' => 'readonly',
            'disabled' => 'disabled',
        ]);

        // First name
        $firstName = new Element\Text('first_name');
        $firstName->setLabelAttributes([
            'for' => $formName . $firstName->getAttribute('name'),
        ]);
        $firstName->setAttributes([
            'id' => $formName . $firstName->getAttribute('name'),
            'class' => 'uk-input',
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

        // Role
        $role = new Element\Select('role');
        $role->setLabelAttributes([
            'for' => $formName . $role->getAttribute('name'),
        ]);
        $role->setAttributes([
            'id' => $formName . $role->getAttribute('name'),
            'class' => 'uk-select',
            'placeholder' => 'Role',
        ]);

        // Submit button
        $submit = new Element\Submit('submit');
        $submit->setAttributes([
            'id' => $formName . $submit->getAttribute('name'),
            'value' => 'Save',
            'class' => 'uk-button uk-button-primary',
            'type' => 'submit'
        ]);

        // Add elements to form
        $this->add($id);
        $this->add($email);
        $this->add($firstName);
        $this->add($lastName);
        $this->add($role);
        $this->add($submit);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        // Id
        $id = [
            'required' => false,
        ];

        // Email
        $email = [
            'required' => false,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
                ['name' => 'StripNewLines'],
            ],
            'validators' => [
                ['name' => 'EmailAddress'],
                [
                    'name' => '\App\Forms\Validators\UniqueEmailInDB',
                    'options' => [
                        'container' => $this->getOption('container'),
                        'ignore_self' => true
                    ]
                ],
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

        // First Name
        $firstName = [
            'required' => false,
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

        // Role
        $role = [
            'required' => false,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
                ['name' => 'StripNewLines'],
            ],
            'validators' => [
                [
                    'name' => '\App\Forms\Validators\IsRole',
                    'options' => [
                        'container' => $this->getOption('container'),
                    ]
                ]
            ]
        ];

        return [
            'id' => $id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'role' => $role,
        ];
    }
}
