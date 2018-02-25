<?php

$app->any('/', \App\MVC\Controllers\Index\IndexController::class . ':index')->setName('home');
$app->get('/test/{id}', \App\MVC\Controllers\Index\IndexController::class . ':test')->setName('test');
