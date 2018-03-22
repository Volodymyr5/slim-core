<?php

// CSRF Tokens View Helper
$view->getEnvironment()->addFunction(new \Twig_Function('csrf_tokens', function () use ($container) {
    $csrfTokens = new \App\MVC\ViewHelpers\CsrfTokensHelper($container);

    return $csrfTokens->render();
}));

// Assets View Helper
$view->getEnvironment()->addFunction(new \Twig_Function('assets', function () use ($container) {
    $path = func_get_arg(0) ? func_get_arg(0) : '';
    $assets = new \App\MVC\ViewHelpers\AssetsHelper($container, $path);

    return $assets->render();
}));

// Show Flash Messages View Helper
$view->getEnvironment()->addFunction(new \Twig_Function('show_flash_messages', function () use ($container) {
    $showFlashMessages = new \App\MVC\ViewHelpers\ShowFlashMessagesHelper($container);

    return $showFlashMessages->render();
}));

// Get Config View Helper
$view->getEnvironment()->addFunction(new \Twig_Function('get_config', function () use ($container) {
    $configName = func_get_arg(0) ? func_get_arg(0) : '';
    $config = new \App\MVC\ViewHelpers\GetConfigHelper($container, $configName);

    return $config->render();
}));

// Get Site Url Helper
$view->getEnvironment()->addFunction(new \Twig_Function('get_site_url', function () use ($container) {
    $config = new \App\MVC\ViewHelpers\GetSiteUrlHelper($container);

    return $config->render();
}));

// Get Is Route Allowed Helper
$view->getEnvironment()->addFunction(new \Twig_Function('is_route_allowed', function () use ($container) {
    $routeName = func_get_arg(0) ? func_get_arg(0) : '';
    $assets = new \App\MVC\ViewHelpers\IsRouteAllowed($container, $routeName);

    return $assets->render();
}));