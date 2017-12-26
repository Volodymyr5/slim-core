<?php

//$app->get('/', 'Index\IndexController:index');
$app->get('/', \App\Controllers\Index\IndexController::class . ':index');
$app->get('/test/{id}', \App\Controllers\Index\IndexController::class . ':test');
