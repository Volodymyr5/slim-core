<?php

$dbSettings = isset($container['settings']['custom']['db']) && is_array($container['settings']['custom']['db'])
    ? $container['settings']['custom']['db'] : [];

// Switch DB type
$dbType = false;

// Check SQLight
if (
    !empty($dbSettings['sqlight']['file_path']) &&
    file_exists($dbSettings['sqlight']['file_path'])
) {
    $dbType = 'sqlight';
}

// Check MySql
if (
    !empty($dbSettings['mysql']['host']) &&
    !empty($dbSettings['mysql']['dbname']) &&
    !empty($dbSettings['mysql']['username']) &&
    !empty($dbSettings['mysql']['password'])
) {
    $dbType = 'mysql';
}

// Setup Paris ORM
switch ($dbType) {
    case 'sqlight':
        \ORM::configure('sqlite:' . $dbSettings['sqlight']['file_path']);
        break;
    case 'mysql':
        \ORM::configure('mysql:host=' . $dbSettings['mysql']['host'] . ';dbname=' . $dbSettings['mysql']['dbname']);
        \ORM::configure('username', $dbSettings['mysql']['username']);
        \ORM::configure('password', $dbSettings['mysql']['password']);
        break;
    default:
        throw new \Exception('Error. Wrong Data Base connection!');
}

\ORM::configure('logging', true);