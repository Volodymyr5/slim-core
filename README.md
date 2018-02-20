# Slim Framework Skeleton

## Contain
1. [Map Routing to Controller `app/routes/*.php`](#1-map-routing-to-controller-approutesphp)
2. [Twig View, Layout and `path_for()` - path builder `app/views` | `"slim/twig-view"`](#2-twig-view-layout-and-path_for---path-builder-appviews--slimtwig-view)
3. [CSRF protection with method - `{{ csrf_tokens() | raw }}` | `slim/csrf`](#3-csrf-protection-with-method)
4. [ORM for SQLite and MySQL with - `"j4mie/paris"`](#4-orm-for-sqlite-and-mysql-with---j4mieparis)
5. [Form builder and validator from ZF2 example from - `github/akrabat/slim-zendform`](#5-form-builder-and-validator)
6. [SMTP Mailer example from - `github/swt83/php-smtp`](#6-smtp-mailer-example-from)
7. [Alert and Notifications using - `slim/flash`](#7-alert-and-notifications)

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

3\. Current route variable - `currentRoute`

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
 * Class ClassName
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
$this->sendMail([
    // required
    'to' => ['to1@mail.com', 'to2@mail.com'],
    'subject' => 'Mail Subject',
    'body' => 'Mail <b>Body</b>.',
    // not required
    'from_name' => 'From Name',
    'copy' => ['copy1@mail.com', 'copy2@mail.com'],
    'hidden_copy' => ['hidden_copy1@mail.com', 'hidden_copy2@mail.com'],
    'attachments' => ['/file/full/path/1.jpg', '/file/full/path/2.jpg']
]);
```

#### More info and details
[https://github.com/swt83/php-smtp](https://github.com/swt83/php-smtp)

### 7. Alert and Notifications
#### Usage:
In Controller use one of:
```php
$this->container->flash->addMessage('primary', '<h4>This is a Primary Notification</h4>');
$this->container->flash->addMessage('success', '<h4>This is a Success Notification</h4>');
$this->container->flash->addMessage('warning', '<h4>This is a Warning Notification</h4>');
$this->container->flash->addMessage('danger', '<h4>This is a Danger Notification</h4>');

$this->container->flash->addMessage('alert-primary', '<h4>This is a Primary Alert</h4>');
$this->container->flash->addMessage('alert-success', '<h4>This is a Success Alert</h4>');
$this->container->flash->addMessage('alert-warning', '<h4>This is a Warning Alert</h4>');
$this->container->flash->addMessage('alert-danger', '<h4>This is a Danger Alert</h4>');
```

To allow Alerts and Notification in your custom layout use View Helper:
```twig
{{ show_flash_messages() | raw }}
```

#### More info and details
[https://github.com/slimphp/Slim-Flash](https://github.com/slimphp/Slim-Flash)