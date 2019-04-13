<?php

/**
 * This is the base config. It doesn't hold any informations about the database and is only used for local development.
 * Use env-local-db.php to configure you database.
 */

/*
 * Enable or disable the debugging, if those values are deleted YII_DEBUG is false and YII_ENV is prod.
 * The YII_ENV value will also be used to load assets based on environment (see assets/ResourcesAsset.php)
 */
defined('YII_ENV') or define('YII_ENV', 'local');
defined('YII_DEBUG') or define('YII_DEBUG', true);

$params = require __DIR__ . '/params.php';

$config = [
    /*
     * For best interoperability it is recommend to use only alphanumeric characters when specifying an application ID.
     */
    'id' => 'bdavn',
    /*
     * The name of your site, will be display on the login screen
     */
    'siteTitle' => 'bdavn',
    /*
     * Let the application know which module should be executed by default (if no url is set). This module must be included
     * in the modules section. In the most cases you are using the cms as default handler for your website. But the concept
     * of LUYA is also that you can use a website without the CMS module!
     */
    'defaultRoute' => 'news',
    /*
     * Define the basePath of the project (Yii Configration Setup)
     */
    'basePath' => dirname(__DIR__),
    'modules' => [
        /*
         * If you have other admin modules (e.g. cmsadmin) then you going to need the admin. The Admin module provides
         * a lot of functionality, like storage, user, permission, crud, etc. But the basic concept of LUYA is also that you can use LUYA without the
         * admin module.
         *
         * @secureLogin: (boolean) This will activate a two-way authentication method where u get a token sent by mail, for this feature
         * you have to make sure the mail component is configured correctly. You can test this with console command `./vendor/bin/luya health/mailer`.
         */
        'admin' => [
            'class' => 'luya\admin\Module',
            'secureLogin' => false, // when enabling secure login, the mail component must be proper configured otherwise the auth token mail will not send.
            'strongPasswordPolicy' => false, // If enabled, the admin user passwords require strength input with special chars, lower, upper, digits and numbers
            'interfaceLanguage' => 'en', // Admin interface default language. Currently supported: en, de, ru, es, fr, ua, it, el, vi, pt, fa
        ],
        /*
         * Frontend module for the `cms` module.
         */
        'cms' => [
            'class' => 'luya\cms\frontend\Module',
            'contentCompression' => true, // compressing the cms output (removing white spaces and newlines)
        ],
        /*
         * Admin module for the `cms` module.
         */
        'cmsadmin' => [
            'class' => 'luya\cms\admin\Module',
            'hiddenBlocks' => [],
            'blockVariations' => [],
        ],
        'news' => [
            'class' => 'app\modules\news\frontend\Module',
            'useAppViewPath' => false, // When enabled the views will be looked up in the @app/views folder, otherwise the views shipped with the module will be used.
        ],
        'newsadmin' => 'app\modules\news\admin\Module',
    ],
    'components' => [
        /*
         * Add your smtp connection to the mail component to send mails (which is required for secure login), you can test your
         * mail component with the luya console command ./vendor/bin/luya health/mailer.
         */
        'mail' => [
            'host' => null,
            'username' => null,
            'password' => null,
            'from' => null,
            'fromName' => null,
        ],
        /*
         * The composition component handles your languages and they way your urls will look like. The composition components will
         * automatically add the language prefix which is defined in `default` to your url (the language part in the url  e.g. "yourdomain.com/en/homepage").
         *
         * hidden: (boolean) If this website is not multi lingual you can hide the composition, other whise you have to enable this.
         * default: (array) Contains the default setup for the current language, this must match your language system configuration.
         */
        // 'composition' => [
        //     'hidden' => true, // no languages in your url (most case for pages which are not multi lingual)
        //     'default' => ['langShortCode' => 'en'], // the default language for the composition should match your default language shortCode in the language table.
        // ],
        /*
         * If cache is enabled LUYA will cache cms blocks and speed up the system in different ways. In the prep config
         * we use the DummyCache to imitate the caching behavior, but actually nothing gets cached. In production you should
         * use caching which matches your hosting environment. In most cases yii\caching\FileCache will result in fast website.
         *
         * http://www.yiiframework.com/doc-2.0/guide-caching-data.html#cache-apis
         */
        'cache' => [
            'class' => 'yii\caching\FileCache', // use: yii\caching\FileCache
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'search/' => 'search/index',
                '<controller:\w+>/<action:\w+>/' => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>/' => '<module>/<controller>/<action>',
            ],
        ],
        'storage' => [
            'class' => 'app\components\LocalFileSystem',
            // 'httpPath' => '',
            // 'absoluteHttpPath' => '',
            // 'serverPath' => '',
        ],
        'socialShare' => [
            'class' => \ymaker\social\share\configurators\Configurator::class,
            'enableIcons' => true,
            'socialNetworks' => [
                'facebook' => [
                    'class' => \ymaker\social\share\drivers\Facebook::class,
                    'options' => ['class' => 'facebook']
                ],
                'twitter' => [
                    'class' => \ymaker\social\share\drivers\Twitter::class,
                    'options' => ['class' => 'twitter']
                ],
                // 'Gmail' => [
                //     'class' => \ymaker\social\share\drivers\Gmail::class,
                // ],
                'linkedIn' => [
                    'class' => \ymaker\social\share\drivers\LinkedIn::class,
                    'options' => ['class' => 'linkedin']
                ],
                'pinterest' => [
                    'class' => \ymaker\social\share\drivers\Pinterest::class,
                    'options' => ['class' => 'pinterest']
                ],
                'whatsApp' => [
                    'class' => \ymaker\social\share\drivers\WhatsApp::class,
                    'options' => ['class' => 'whatsapp']
                ],
            ],
            'icons' => [
                \ymaker\social\share\drivers\Twitter::class => 'fab fa-twitter-square', // CSS class
                \ymaker\social\share\drivers\Facebook::class => 'fab fa-facebook-square',  // CSS class
                //\ymaker\social\share\drivers\Gmail::class => 'fab google-plus-square',  // CSS class
                \ymaker\social\share\drivers\LinkedIn::class => 'fab fa-linkedin-square',  // CSS class
                \ymaker\social\share\drivers\Pinterest::class => 'fab fa-pinterest-square',  // CSS class
                \ymaker\social\share\drivers\WhatsApp::class => 'fab fa-whatsapp-square',  // CSS class
            ]
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            // 'db' => 'mydb',  // the application component ID of the DB connection. Defaults to 'db'.
            // 'sessionTable' => 'my_session', // session table name. Defaults to 'session'.
        ],
    ],
    'params' => $params,
    'tags' => [
        'tooltip' => ['class' => 'app\tags\TooltipTag'],
    ],
    'on beforeRequest' => function ($event) {
        $event->sender->response->on(yii\web\Response::EVENT_BEFORE_SEND, function($e){
            ob_start("ob_gzhandler");
        });
        $event->sender->response->on(yii\web\Response::EVENT_AFTER_SEND, function($e){
            ob_end_flush();
        });
    },
];

//*
if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = ['class' => 'yii\debug\Module', 'allowedIPs' => ['127.0.0.1']];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = ['class' => 'yii\gii\Module', 'allowedIPs' => ['127.0.0.1', '::1'],];
}
//*/

return \yii\helpers\ArrayHelper::merge($config, require('env-local-db.php'));
