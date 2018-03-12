<?php
// Add csrf middleware
$container->csrf->setPersistentTokenMode(true);
$app->add($container->csrf);

// Add currentRoute variable to Twig global
$app->add(function ($request, $response, $next) use ($container) {
    // retrieve value of route
    $route = $request->getAttribute('route');
    $name = false;
    if (!empty($route)) {
        $name = $route->getName();
    }

    // set global variable
    $twig = $container->view->getEnvironment();
    $twig->addGlobal('currentRoute', $name);

    // Continue middleware chain
    return $next($request, $response);
});

// JWT auth processing
$app->add(function ($request, $response, $next) use ($container) {
    $container->user->update();
    //$container->user->update();
    // Continue middleware chain
    return $next($request, $response);
});