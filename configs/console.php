<?php

$params = require __DIR__ . '/params.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'storage' => [
            'class' => 'app\components\LocalFileSystem',
            'folderName' => 'storage',
            //'httpPath' => 'storage',
            //'absoluteHttpPath' => 'storage',
            // 'serverPath' => '',
        ]
    ],
    'params' => $params,
];

return \yii\helpers\ArrayHelper::merge($config, require('env-prod-db.php'));