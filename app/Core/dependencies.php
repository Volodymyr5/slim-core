<?php

// Inject Slim CSRF
$container['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard;
};

// Disable Slim Error handler
unset($container['errorHandler']);
unset($container['phpErrorHandler']);

// Inject Slim-Twig
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../MVC/views', [
        'cache' => false,
        'debug' => true,
    ]);

    $view->addExtension(new \Twig_Extension_Debug());

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    // Twig view helpers
    require __DIR__ . '/twig_functions.php';

    return $view;
};

// Inject ZF2 form bulider and validator
$smConfigurator = new \App\Core\ServiceManagerConfigurator();
$containerServiceManager = $smConfigurator->createServiceManager([]);
$container['serviceManager'] = $containerServiceManager;

// Set up Twig callback function
$viewHelperManager = $containerServiceManager->get('ViewHelperManager');
$renderer = new \Zend\View\Renderer\PhpRenderer();
$renderer->setHelperPluginManager($viewHelperManager);

$view = $container['view'];

$view->getEnvironment()->registerUndefinedFunctionCallback(
    function ($name) use ($viewHelperManager, $renderer) {
        if (!$viewHelperManager->has($name)) {
            return false;
        }
        $callable = [$renderer->plugin($name), '__invoke'];
        $options = ['is_safe' => ['html']];

        return new \Twig_SimpleFunction($name, $callable, $options);
    }
);

// Inject Slim-Flash
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// Inject JWT Auth
$container['user'] = function () use ($container) {
    return new \App\Core\Libs\Auth($container);
};