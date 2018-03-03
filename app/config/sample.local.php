<?php

return [
    'db' => [
        // SQLight
        'sqlight' => [
            'file_path' => __DIR__ . '/db/core.db',
        ],
        // MySql
        'mysql' => [
            'host' => 'localhost',
            'dbname' => 'dbname',
            'username' => 'username',
            'password' => 'password',
        ],
    ],
    'jwt' => [
        'access_token' => 60*10,
        'refresh_token' => 60*60*24,
        'public' =>
<<<EOT
-----BEGIN PUBLIC KEY-----
-----END PUBLIC KEY-----
EOT
        ,
        'private' =>
<<<EOT
-----BEGIN RSA PRIVATE KEY-----
-----END RSA PRIVATE KEY-----
EOT
    ],
    'smtp' => [
        // Debug mode will echo connection status alerts to
        // the screen throughout the email sending process.
        // Very helpful when testing your credentials.
        'debug_mode' => true,
        // Define the different connections that can be used.
        // You can set which connection to use when you create
        // the SMTP object: ``$mail = new SMTP('my_connection')``.
        'default' => 'primary',
        'connections' => [
            'primary' => [
                'host' => '',
                'port' => '',
                'secure' => null, // null, 'ssl', or 'tls'
                'auth' => false, // true if authorization required
                'user' => '',
                'pass' => '',
            ],
        ],
        // NERD ONLY VARIABLE: You may want to change the origin
        // of the HELO request, as having the default value of
        // "localhost" may cause the email to be considered spam.
        // http://stackoverflow.com/questions/5294478/significance-of-localhost-in-helo-localhost
        'localhost' => 'localhost', // rename to the URL you want as origin of email
    ]
];
