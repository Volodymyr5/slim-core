<?php
session_start();

// Composer autoload
require __DIR__ . '/vendor/autoload.php';

$customSettings = require_once __DIR__ . '/settings.php';

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
require __DIR__ . '/src/dbconnection.php';

// Fill requirements in Container
require __DIR__ . '/src/dependencies.php';

// Add Middleware
require __DIR__ . '/src/middleware.php';

// Add routes
require __DIR__ . '/routes/index.php';
