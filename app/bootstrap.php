<?php
session_start();

// Composer autoload
require __DIR__ . '/vendor/autoload.php';

// App settings
$customSettings = require_once __DIR__ . '/config/settings.php';
try {
    $localSettings = require_once __DIR__ . '/config/local.php';
} catch (\Exception $e) {
    $localSettings = [];
}
$customSettings = array_merge($customSettings, $localSettings);

// Slim app instance
$app = new \Slim\App([
    'settings' => [
        'debug' => true,
        'whoops.editor' => 'sublime',
        'custom' => $customSettings,
        'determineRouteBeforeAppMiddleware' => true,
    ],
]);

// Get dependency injection Container
$container = $app->getContainer();

// Paris ORM setup
require __DIR__ . '/Core/dbconnection.php';

// Fill requirements in Container
require __DIR__ . '/Core/dependencies.php';

// Add Middleware
require __DIR__ . '/Core/middleware.php';

// Add routes
require __DIR__ . '/MVC/routes/auth.php';
require __DIR__ . '/MVC/routes/index.php';
