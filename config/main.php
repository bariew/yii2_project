<?php
Yii::setAlias('@app', dirname(__DIR__));
require_once '_events.php';
return \yii\helpers\ArrayHelper::merge([
    'id' => 'app',
    'name'  => 'Pragmi',
    'language'  => 'en',
    'timeZone' => 'Europe/Moscow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@webroot' => dirname(__DIR__) . '/web',
        '@vendor' => dirname(__DIR__) . '/vendor',
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => dirname(__DIR__) .'/tests',
        '@shared' => dirname(__DIR__) .'/web', // console overwrites @webroot
    ],
    'layoutPath' => '@app/modules/common/views/layouts',
    'components' => [
        'assetManager' => ['appendTimestamp' => true, 'linkAssets' => true,],
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
        'authManager' => ['class' => \yii\rbac\DbManager::class, 'defaultRoles' => ['guest'],],
        'cache'=>['class'=>'yii\caching\FileCache', 'dirMode' => 0777, 'fileMode' => 0777,],
        'db'    => [
            'class' => '\yii\db\Connection',
            'dsn'   => 'mysql:host=localhost;dbname=yii2_project',
            'charset'=>'utf8mb4',
            'enableSchemaCache' => true,
            'enableQueryCache'=>true,
            'queryCacheDuration'=>3600,
            'on afterOpen' => function(\yii\base\Event $event) {
                $sender = $event->sender;/** @var \yii\db\Connection $sender */
                $sender->createCommand("SET time_zone='+00:00'; SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));")->execute();
            },
        ],
        'i18n' => ['translations' => [
            '*' => ['class' => 'yii\i18n\DbMessageSource', 'enableCaching' => true],
            'yii' => ['class' => 'yii\i18n\DbMessageSource', 'enableCaching' => true]
        ]],


        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
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
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info'],
                    'logTable' => '{{%log_error}}',
                    'exportInterval' => 1,
                    'except'  => ['yii\db\*', 'yii\web\*', 'yashop\ses\Mailer*', 'yii\mail\BaseMailer*'],
                ],
            ],
        ],

        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'htmlLayout' => 'layout_default',
            'viewPath' => '@app/modules/common/views/mail',
            'transport' => [
                'dsn' => 'sendmail://default',
            ],
//            'transport' => [
//                'scheme' => 'smtps',
//                'host' => '',
//                'username' => '',
//                'password' => '',
//                'port' => 465,
//                'dsn' => 'native://default',
//            ],
        ],
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
                '<module>s' => '<module>/default/index',
                '<module>/<id:\d+>' => '<module>/default/view',
                '<module>/<id:\d+>/<key:\w+>' => '<module>/default/share',
                '<module>/<action:(update|delete)>/<id:\d+>' => '<module>/default/<action>',
                '<module>/<controller>/<id:\d+>' => '<module>/<controller>/view',
                '<module>/<action>/pr<project_id:\d+>' => '<module>/default/<action>',
                '<module>/<controller>ies' => '<module>/<controller>y/index',
                '<module>/<controller>ses' => '<module>/<controller>s/index',
                '<module>/<controller:(department)>s' => '<module>/<controller>/index',
                '<module>/<action>' => '<module>/default/<action>',
                //'<module>/<controller>' => '<module>/<controller>/index',

                '<module>/<controller>/<action>/<id:\w+>' => '<module>/<controller>/<action>',
                '<module>/<controller>/<action>/pr<project_id:\d+>' => '<module>/<controller>/<action>',
                '<module>/<controller>/<action>' => '<module>/<controller>/<action>',
            ],
        ],

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
        'bsVersion' => 5, //for kartik widgets
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
