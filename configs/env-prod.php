<?php
defined('YII_ENV') or define('YII_ENV', 'prod');
defined('YII_DEBUG') or define('YII_DEBUG', false);

return [
    'id' => 'bdavn',
    'siteTitle' => 'bdavn',
    'defaultRoute' => 'cms',
    'basePath' => dirname(__DIR__),
    'modules' => [
        'admin' => [
            'class' => 'luya\admin\Module',
            'secureLogin' => true,
            'interfaceLanguage' => 'vi',
        ],
        'cms' => [
            'class' => 'luya\cms\frontend\Module',
        ],
        'cmsadmin' => 'luya\cms\admin\Module',
    ],
    'components' => [
        'mail' => [
            'host' => null,
            'username' => null,
            'password' => null,
            'from' => null,
            'fromName' => null,
        ],
        'errorHandler' => [
            'transferException' => true,
        ],
        'composition' => [
            'hidden' => true, 
            'default' => ['langShortCode' => 'vi'],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache', // use: yii\caching\FileCache
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=bdavn',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 43200,
        ],
        /*
    	 * Translation component. If you don't have translations just remove this component and the folder `messages`.
    	 */
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
            ],
        ],
    ],
    'bootstrap' => [
        'cms',
    ],
];