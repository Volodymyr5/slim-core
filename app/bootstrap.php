<?php
session_start();

// Composer autoload
require __DIR__ . '/vendor/autoload.php';

// Slim app instance
$app = new \Slim\App([
    'settings' => [
        'displayErrrorDetails' => true,
        'debug' => true,
        'whoops.editor' => 'sublime',
    ],
]);

// Get dependency injection Container
$container = $app->getContainer();

// Fill requirements in Container
require __DIR__ . '/src/dependencies.php';

// Add Middleware
require __DIR__ . '/src/middleware.php';

// Add routes
require __DIR__ . '/routes/index.php';
