<?php
session_start();

// Composer autoload
require __DIR__ . '/vendor/autoload.php';

// App settings
$customSettings = require_once __DIR__ . '/config/settings.php';
$localSettings = require_once __DIR__ . '/config/local.php';
$customSettings = array_merge($customSettings, $localSettings);

// Slim app instance
$app = new \Slim\App([
    'settings' => [
        'displayErrrorDetails' => true,
        'debug' => true,
        'whoops.editor' => 'sublime',
        'custom' => $customSettings,
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
require __DIR__ . '/routes/index.php';
