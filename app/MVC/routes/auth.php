<?php

$app->any('/login', \App\MVC\Controllers\Auth\IndexController::class . ':index')->setName('login');
$app->any('/register', \App\MVC\Controllers\Auth\IndexController::class . ':register')->setName('register');
$app->get('/logout', \App\MVC\Controllers\Auth\IndexController::class . ':logout')->setName('logout');
$app->get('/set-password', \App\MVC\Controllers\Auth\IndexController::class . ':setPassword')->setName('set-password');

$app->any('/tm', \App\MVC\Controllers\Auth\IndexController::class . ':testEmail')->setName('tm');