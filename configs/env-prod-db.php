<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=askbda',
            // 'dsn' => 'mysql:host=localhost;dbname=DB_NAME;unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock', // OSX MAMP
            // 'dsn' => 'mysql:host=localhost;dbname=DB_NAME;unix_socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock', // OSX XAMPP
            'username' => 'bdacomadm',
            'password' => '!@#$bda$#@!',
            'charset' => 'utf8',

            // in productive environments you can enable the schema caching
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 43200,
        ]
    ]
];
