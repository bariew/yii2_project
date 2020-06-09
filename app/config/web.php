<?php
require_once __DIR__ . '/bootstrap.php';
$localConfig = \yii\helpers\ArrayHelper::merge(
    require __DIR__ . DIRECTORY_SEPARATOR . 'project.php',
    require __DIR__ . DIRECTORY_SEPARATOR . 'local.php'
);
return \yii\helpers\ArrayHelper::merge([
    'id' => 'app',
    'name'  => 'Yii2 Project',
    'language'  => 'en',
    'timeZone' => 'Europe/Moscow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@webroot' => dirname(__DIR__) . '/../web',
        '@vendor' => dirname(__DIR__) . '/../vendor',
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => dirname(dirname(__DIR__)) .'/tests',
        '@shared' => dirname(dirname(__DIR__)) .'/web', // console overwrites @webroot
    ],
    'components' => [
        'db'    => [
            'class' => '\yii\db\Connection',
            'dsn'   => 'mysql:host=localhost;dbname=yii2_project',
        ],
        'user' => [
            'identityClass' => 'app\\modules\\user\\models\\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/default/login']
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
        'assetManager' => [
            'appendTimestamp' => true,
            'linkAssets' => true,
        ],
        'urlManager' => [
            'enablePrettyUrl'       => true,
            'showScriptName'        => false,
            'enableStrictParsing'   => true,
            'rules' => [
                [
                    'class' => \app\modules\page\components\UrlRule::class,
                    'pattern' => '<url:\\S*>',
                    'route' => 'page/default/view',
                    //'enforceSeo' => true,
                ],
                '/' => 'site/index',
                '<_c>/<_a>' => '<_c>/<_a>',
                '<_m>/<_c>/<_a>' => '<_m>/<_c>/<_a>',
                '<is_api:api>/<_m>/<_c>/<_a>' => '<_m>/<_c>/<_a>',
            ],
        ],
        'i18n'  => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
            ],
        ],
        'request'   => [
            'cookieValidationKey'   => 'someValidationKey'
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/modules' => '@app/views',
                    '@app/mail' => '@app/views/mail',
                ],
            ],
            'renderers' => [
                'js' => \app\modules\test\ReactRenderer::class,
                'jsx' => \app\modules\test\ReactRenderer::class,
            ]
        ],
        'authManager' => [
            'class' => \yii\rbac\PhpManager::class,
            'defaultRoles' => ['guest'],
        ],
        'cache'=>[
            'class'=>'yii\caching\FileCache',//'yii\caching\MemCache',
            'dirMode' => 0777,
            'fileMode' => 0777,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
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
            'useFileTransport'=>false,
            'htmlLayout' => 'layout_default',
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
        'log' => ['class' => 'app\\modules\\log\\Module'],
        'user' => ['class' => 'app\\modules\\user\\Module'],
        'page' => ['class' => 'app\\modules\\page\\Module'],
        //'rbac' => ['class' => 'app\\modules\\rbac\\Module'],
        'post' => ['class' => 'app\\modules\\post\\Module'],
        'comment' => ['class' => 'app\\modules\\comment\\Module'],
    ],
    'params' => [
        'bsVersion' => 4, //for kartik widgets
        'baseUrl' => 'http://localhost:8080',
        'admin_email' => 'admin@test.com',
        'salt' => 'a689839638e2243145ac9b2683cac9bd',
        'languages' => [
            'en' => ['code' => 'en', 'title' => 'English (US)'],
        ],
    ]
], $localConfig);