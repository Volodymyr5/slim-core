# Slim Framework Skeleton

## Contain
1. [Map Routing to Controller `app/routes/*.php`](#map-routing-to-controller-approutesphp)
2. [Twig View, Layout and `path_for()` - path builder `app/views` | `"slim/twig-view"`](#twig-view-layout-and-path_for---path-builder-appviews--slimtwig-view)
3. [CSRF protection with method - `{{ csrf_tokens() | raw }}` | `slim/csrf`](#csrf-protection-with-method)
4. [ORM for SQLite and MySQL with - `"j4mie/paris"`](#orm-for-sqlite-and-mysql-with---j4mieparis)
5. [Form builder and validator from ZF2 example from - `github/akrabat/slim-zendform`](#form-builder-and-validator)
6. [SMTP Mailer example from - `github/swt83/php-smtp`](#smtp-mailer-example-from)

## Description
In project for Controllers use such folder structure:
```text
app \
    |- Controllers \
        |- <Module Name> \
            |- <Class name> \
```
And for Views:
```text
app \
    |- views \
        |- <Module Name> \
            |- <Class name> \
                |- <Action name> \
```

### 1. Map Routing to Controller `app/routes/*.php`
#### Usage
1\. In `app\routes\*.php` add

```php
$app->any('/', \App\Controllers\Index\IndexController::class . ':index')->setName('home');
```

2\. In `app/bootstrap.php`, in `// Add routes` section add

```php
require __DIR__ . '/routes/*.php';
```

### 2. Twig View, Layout and `path_for()` - path builder `app/views` | `"slim/twig-view"`
#### Usage
in any Controller use:
```php
return $this->view->render($response, 'index\index\index.twig', [
    'var' => $var
]);
```

In Twig view you can use:

1\. URL constructor

```twig
{{ path_for('<route name>', { '<route var1>': '<route var1 value>' }) }}
```

2\. URL to assets constructor
```twig
{{ assets('path/to/script.js') }}` - convert to `/base/path/assets/path/to/script.js
```

### 3. CSRF protection with method
#### Usage:
In any form add `{{ csrf_tokens() | raw }}` to pass CSRF check

### 4. ORM for SQLite and MySQL with - `"j4mie/paris"`
#### Usage:
In `app/Models/` create class:
```php
<?php

namespace App\Models;

/**
 * Class <Class name>
 * @package App\Models
 */
class ClassName extends CoreModel {
}
```

`ClassName` - should be the same as table column name

#### More info aboute usage Paris ORM see in docs
[http://paris.readthedocs.io](http://paris.readthedocs.io)

### 5. Form builder and validator
#### Usage:
Create Form class in `app/Forms`
```php
<?php

namespace App\Forms;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class UserForm
 * @package App\Forms
 */
class UserForm extends Form implements InputFilterProviderInterface
{
    public function init()
    {
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email address',
            ],
            'attributes' => [
                'id'       => 'email',
                'class'    => 'uk-input',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'button',
            'options' => [
                'label' => 'Send',
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

```

In Any Controller:
```php
$form = $this->getForm('App\Forms\UserForm');

if ($request->isPost()) {
    $data = $request->getParams();
    $form->setData($data);
    $isValid = $form->isValid();
    if ($isValid) {
        // Do something
    }
}
```

#### More info and details
[https://github.com/akrabat/slim-zendform](https://github.com/akrabat/slim-zendform)

### 6. SMTP Mailer example from
#### Usage:
in any Controller use:
```php
$mailer = $this->getMailer();
if ($mailer) {
    $mailer->to('to1@mail.com');
    $mailer->to('to2@mail.com');
    // add cc
    $mailer->cc('to3@mail.com');
    $mailer->cc('to4@mail.com');
    // add bcc
    $mailer->bcc('to5@mail.com');
    $mailer->bcc('to6@mail.com');
    $mailer->from('from@mail.com', 'From Name');
    $mailer->reply('reply@mail.com', 'Reply Name');
    // add attachment
    $mailer->attach('/path/to/file1.png');
    $mailer->attach('/path/to/file2.png');
    $mailer->subject('Mail Subject');
    $mailer->body('Mail <b>Body</b>.');
    
    $mailer->text('Text version of email.');
    
    $result = $mailer->send();
    // Send only text
    $result = $mailer->send_text();

    var_dump($result);
}
```

#### More info and details
[https://github.com/swt83/php-smtp](https://github.com/swt83/php-smtp)