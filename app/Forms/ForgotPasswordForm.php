<?php

namespace App\Forms;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\StringLength;

/**
 * Class ForgotPasswordForm
 * @package App\Forms
 */
class ForgotPasswordForm extends Form implements InputFilterProviderInterface
{
    /**
     * Init form
     */
    public function init()
    {
        $formName = $this->getName() ? $this->getName() . '-' : '';

        // Email
        $email = new Element\Email('email');
        $email->setLabelAttributes([
            'for' => $formName . $email->getAttribute('name'),
        ]);
        $email->setAttributes([
            'id' => $formName . $email->getAttribute('name'),
            'class' => 'uk-input',
            'required' => 'required',
            'placeholder' => 'Email',
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
        $this->add($email);
        $this->add($submit);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        // Email
        $email = [
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
                ['name' => 'EmailAddress'],
                [
                    'name' => '\App\Forms\Validators\ExistEmailInDB',
                    'options' => [
                        'container' => $this->getOption('container'),
                    ]
                ],
            ]
        ];

        return [
            'email' => $email,
        ];
    }
}
