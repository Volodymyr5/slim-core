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

// Setup idiORM
switch ($dbType) {
    case 'sqlight':
        $pdoConnection = new PDO('sqlite:' . $dbSettings['sqlight']['file_path']);
        break;
    case 'mysql':
        $pdoConnection = new PDO(
            "mysql:host={$dbSettings['mysql']['host']};dbname={$dbSettings['mysql']['dbname']}",
            $dbSettings['mysql']['username'],
            $dbSettings['mysql']['password']
        );
        break;
    default:
        throw new \Exception('Error. Wrong Data Base connection!');
}

// Set errormode to exceptions
if (DEBUG) {
    $pdoConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

$container['db'] = function ($container) use ($pdoConnection) {
    $fpdo = new FluentPDO($pdoConnection);
    //$fpdo->debug = DEBUG;

    return $fpdo;
};
