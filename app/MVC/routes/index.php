<?php

$app->any('/', \App\MVC\Controllers\Index\IndexController::class . ':index')->setName('home');
$app->get('/admin', \App\MVC\Controllers\Index\IndexController::class . ':admin')->setName('admin');

$app->get('/test', function ($request, $response, $args) {
    var_dump('secret route');
})->setName('test');