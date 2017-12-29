<?php

$app->get('/', \App\Controllers\Index\IndexController::class . ':index')->setName('home');
$app->get('/test/{id}', \App\Controllers\Index\IndexController::class . ':test')->setName('test');
