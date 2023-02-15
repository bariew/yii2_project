<?php
require_once __DIR__ . '/bootstrap.php';
return \yii\helpers\ArrayHelper::merge([
    'id' => 'app',
    'name'  => 'Pragmi',
    'language'  => 'en',
    'timeZone' => 'Europe/Moscow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@app' => dirname(__DIR__),
        '@webroot' => dirname(__DIR__) . '/eb',
        '@vendor' => dirname(__DIR__) . '/vendor',
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => dirname(__DIR__) .'/tests',
        '@shared' => dirname(__DIR__) .'/web', // console overwrites @webroot
    ],
    'layoutPath' => '@app/modules/common/views/layouts',
    'components' => [
        'db'    => [
            'class' => '\yii\db\Connection',
            'dsn'   => 'mysql:host=localhost;dbname=yii2_project',
            'charset'=>'utf8mb4',
            'enableSchemaCache' => true,
            'enableQueryCache'=>true,
            'queryCacheDuration'=>3600,
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => '',
                    'clientSecret' => '',
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => '',
                    'clientSecret' => '',
                ],
            ],
        ],
        'assetManager' => ['appendTimestamp' => true, 'linkAssets' => true,],
        'urlManager' => [
            'enablePrettyUrl'       => true,
            'showScriptName'        => false,
            'enableStrictParsing'   => true,
            'rules' => [
                '/' => 'common/site/index',
                '<controller:(site|admin)>/<action>' => 'common/<controller>/<action>',
                [
                    'class' => \app\modules\page\components\UrlRule::class,
                    'pattern' => '<url:\\S*>',
                    'route' => 'page/default/view',
                ],
                '<_m>/<_c>/<_a>' => '<_m>/<_c>/<_a>',
                '<is_api:api>/<_m>/<_c>/<_a>' => '<_m>/<_c>/<_a>',
            ],
        ],
        'i18n' => ['translations' => [
            '*' => ['class' => 'yii\i18n\DbMessageSource', 'enableCaching' => true],
            'yii' => ['class' => 'yii\i18n\DbMessageSource', 'enableCaching' => true]
        ]],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/mail' => '@app/modules/common/views/mail',
                ],
            ],
            'renderers' => [
                'js' => \app\modules\test\ReactRenderer::class,
                'jsx' => \app\modules\test\ReactRenderer::class,
            ]
        ],
        'authManager' => [
            'class' => \yii\rbac\DbManager::class,
            'defaultRoles' => ['guest'],
        ],
        'cache'=>['class'=>'yii\caching\FileCache', 'dirMode' => 0777, 'fileMode' => 0777,],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'except' => [
                        'yii\web\HttpException:400',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:404',
                        'yii\i18n\PhpMessageSource::loadMessages',
                        'yii\web\UnauthorizedHttpException',
                    ],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error'],
                    'logTable' => '{{%log_error}}',
                    'except' => [
                        'yii\web\HttpException:400',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:404',
                        'yii\i18n\PhpMessageSource::loadMessages',
                        'yii\web\UnauthorizedHttpException',
                    ],
                ],
            ],
        ],
//        'authManager'   => [
//            'class' => '\yii\rbac\DbManager',
//            'cache' => 'yii\caching\FileCache',
//            'defaultRoles' => ['user/default/logout', 'user/default/login']
//        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'htmlLayout' => 'layout_default',
            'view' => ['theme' => ['pathMap' => ['@app/mail' => '@app/modules/common/views/mail',],],],
//            'view' => ['theme' => ['pathMap' => ['@app/mail' => '@app/views/mail',],],],
//            'transport' => [
//                'class' => 'Swift_SmtpTransport',
//                'host' => 'smtp.gmail.com',
//                'username' => 'xxxxxx',
//                'password' => 'xxxxx',
//                'port' => '465',
//                'encryption' => 'ssl',
//            ],
        ],
    ],
    'modules' => [
        'common' => ['class' => 'app\\modules\\common\\Module'],
        'log' => ['class' => 'app\\modules\\log\\Module'],
        'user' => ['class' => 'app\\modules\\user\\Module'],
        'page' => ['class' => 'app\\modules\\page\\Module'],
        'i18n' => ['class' => 'app\\modules\\i18n\\Module'],
        'rbac' => ['class' => 'app\\modules\\rbac\\Module'],
        'post' => ['class' => 'app\\modules\\post\\Module'],
        'comment' => ['class' => 'app\\modules\\comment\\Module'],
        'nalog' => ['class' => 'app\\modules\\nalog\\Module'],
        'datecontrol' => ['class' => '\kartik\datecontrol\Module'],//, 'convertAction' => '/common/site/date-convert'],
    ],
    'params' => [
        'bsVersion' => 4, //for kartik widgets
        'adminMail' => 'admin@test.com',
        'salt' => 'a689839638e2243145ac9b2683cac9bd',
        'hostInfo' => 'http://localhost:8080',
        'languages' => [
            'he' => 'עברית',
            'en' => 'English',
            'el' => 'Ελληνικά',
            'uk' => 'Українська',
            'ru' => 'Русский',
            'ar' => 'عربى',
            'de' => 'Deutsch',
            'es' => 'Español',
            'fr' => 'Français',
            'pt' => 'Português',
            //'nl' => 'Nederlands',
            'it' => 'Italiano',
        ],
    ]
], require __DIR__ . DIRECTORY_SEPARATOR . 'local.php');
