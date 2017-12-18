<?php
session_start();

// Composer autoload
require __DIR__ . '/vendor/autoload.php';

// Slim app instance
$app = new \Slim\App([
    'settings' => [
        'displayErrrorDetails' => true,
    ],
]);

// Get dependency injection container
$container = $app->getContainer();

// Inject Slim-Twig
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/views', [
        'cache' => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};

// Binding Controllers to routes
$container['Index\IndexController'] = function ($container) {
    return new \App\Controllers\Index\IndexController($container);
};

require __DIR__ . '/routes/index.php';
