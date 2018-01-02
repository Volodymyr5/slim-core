<?php

// Inject Slim CSRF
$container['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard;
};

/*$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        $message = '<h1>Slim Application Error</h1><p>A website error has occurred. Sorry for the temporary inconvenience.</p>';
        $message .= '<p>' . $exception->getMessage() . '</p>';
        $message .= '<pre>' . $exception->getTraceAsString() . '</pre>';

        return $container['response']->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write($message);
    };
};*/

// Disable Slim Error handler
unset($container['errorHandler']);
unset($container['phpErrorHandler']);

// Inject Slim-Twig
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../views', [
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
