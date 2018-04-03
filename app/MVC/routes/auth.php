<?php

$app->any('/login', \App\MVC\Controllers\Auth\LoginController::class . ':index')->setName('login');
$app->any('/register', \App\MVC\Controllers\Auth\RegisterController::class . ':index')->setName('register');
$app->get('/logout', \App\MVC\Controllers\Auth\LoginController::class . ':logout')->setName('logout');
$app->any('/forgot-password', \App\MVC\Controllers\Auth\ForgotPasswordController::class . ':index')->setName('forgot-password');
$app->any('/set-password', \App\MVC\Controllers\Auth\SetPasswordController::class . ':index')->setName('set-password');