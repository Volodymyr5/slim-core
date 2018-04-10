# Slim Framework Skeleton

## Contain
1. [Map Routing to Controller `app/MVC/routes/*.php`](#1-map-routing-to-controller-approutesphp)
2. [Twig View, Layout and View Helpers](#2-twig-view-layout-and-view-helpers)
3. [PDO wrapper for SQLite and MySQL with - `"envms/fluentpdo"`](#3-pdo-wrapper-for-sqlite-and-mysql-with---envms/fluentpdo)
4. [Form builder and validator from ZF2 example from - `github/akrabat/slim-zendform`](#4-form-builder-and-validator)
5. [SMTP Mailer example from - `github/swt83/php-smtp`](#5-smtp-mailer-example-from)
6. [Alert and Notifications using - `slim/flash`](#6-alert-and-notifications)
7. [Access Control List](#6-access-control-list)

## Description
In project for Controllers use such folder structure:
```text
app \ MVC \
    |- Controllers \
        |- <Module Name> \
            |- <Class name> \
```
And for Views:
```text
app \ MVC \
    |- views \
        |- <Module Name> \
            |- <Class name> \
                |- <Action name> \
```

### 1. Map Routing to Controller `app/MVC/routes/*.php`
#### Usage
1\. In `app\MVC\routes\*.php` add

```php
$app->any('/', \App\MVC\Controllers\Index\IndexController::class . ':index')->setName('home');
```

2\. In `app/bootstrap.php`, in `// Add routes` section add

```php
require __DIR__ . '/routes/*.php';
```

### 2. Twig View, Layout and View Helpers - `"slim/twig-view"`
#### Usage
in any Controller use:
```php
return $this->view->render($response, 'index\index\index.twig', [
    'var' => $var
]);
```

In Twig view you can use:

1\. **URL constructor**

```twig
{{ path_for('<route name>', { '<route var1>': '<route var1 value>' }) }}
```

2\. **URL to assets constructor**
```twig
{{ assets('path/to/script.js') }}` - convert to `/base/path/assets/path/to/script.js
```

3\. **Current route variable - `currentRoute`**

4\. **csrf_tokens()**
CSRF protection with method - `{{ csrf_tokens() | raw }}`. Use it inside all your ```<form>``` in views. It will process CSRF check.

5\. **get_site_url()**
With method - `{{ get_site_url() }}` you can receive full path for your website in view
 
6\. **is_route_allowed('route_name')**
Use it to check if is need for you route are allowed for current user ```{% if is_route_allowed('route_name') %}{% endif %}```.
As example to show link for protected page.

7\. **is_role('GUEST') | is_role(['USER', 'ADMIN'])**
Use it to check if is current user has need roles. You can put string or array with roles from ```\App\Libs\Acl->__construct```
If you set wrong role name it will be skipped!

8\. **is_xhr()**
Return ```true``` if route loaded via XHR in other case return ```false```

9\. Link to modal
Add to link ```data-target="sm-modal"``` or ```data-target="lg-modal"``` to load page by link href in small or large modals respectively

10\. Ajax form
Add to form ```ajax-form``` class and it will be processed using Ajax. You can use it in combinations with previous feature

### 3. PDO wrapper for SQLite and MySQL with - `"envms/fluentpdo"`
#### Usage:
In `app/MVC/Models/` create class:
```php
<?php

namespace App\MVC\Models;

/**
 * Class ClassName
 * @package App\MVC\Models
 */
class ClassName extends CoreModel {
}
```

`ClassName` - should be the same as table column name

#### More info about usage FluentPDO see in docs
[http://envms.github.io/fluentpdo/](http://envms.github.io/fluentpdo/)

### 4. Form builder and validator
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

### 5. SMTP Mailer example from
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

To render Email body from Twig view use:
```php
$this->getEmailBody('emails\<view name>', [
    'view' => 'parameters',
]),
```

#### More info and details
[https://github.com/swt83/php-smtp](https://github.com/swt83/php-smtp)

### 6. Alert and Notifications
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

### 7. Access Control List
#### Usage
In ```app/Core/Libs/Acl.php``` inside ```__constructor``` use method
```
$this->addAllowedRole('USER', \App\Core\Constant::ROLE_USER);
```
> \App\Core\Constant::ROLE_USER - used only for convenience, you can provide any integer 
to adding need role in your app (do it **before** call ```parent::__construct```) 

In method ```rules()``` add rules for your **named** routes
> ACL work only with **named** routes. All routes that you don't specify in ACL will be marked as **DENY**.
