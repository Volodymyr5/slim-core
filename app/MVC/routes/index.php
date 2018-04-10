<?php

$app->any('/', \App\MVC\Controllers\Index\IndexController::class . ':index')->setName('home');
$app->get('/admin', \App\MVC\Controllers\Index\AdminController::class . ':index')->setName('admin');
$app->get('/admin/users', \App\MVC\Controllers\Index\AdminController::class . ':users')->setName('admin-users');
$app->any('/admin/users/edit/{id}', \App\MVC\Controllers\Index\AdminController::class . ':usersEdit')->setName('admin-users-edit');
