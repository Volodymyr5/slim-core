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

// Run ACL
$app->add(function ($request, $response, $next) use ($container) {
    $acl = new \App\Core\Libs\Acl($container, $request, $response);

    $container['acl'] = $acl;
    if (!$acl->isAllowed()) {
        return $response->withRedirect('/');
    }

    // Continue middleware chain
    return $next($request, $response);
});