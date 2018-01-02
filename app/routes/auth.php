<?php

$app->any('/login', \App\Controllers\Auth\IndexController::class . ':index')->setName('login');
$app->any('/register', \App\Controllers\Auth\IndexController::class . ':register')->setName('register');
$app->get('/logout', \App\Controllers\Auth\IndexController::class . ':logout')->setName('logout');
$app->get('/confirm', \App\Controllers\Auth\IndexController::class . ':confirm')->setName('confirm');
