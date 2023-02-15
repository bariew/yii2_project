<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');


return [
    'bootstrap' => ['log', 'debug'],
    'components' => [
        'mailer' => [
            'useFileTransport'=>true,
        ],
        'db'    => [
            'dsn'   => 'mysql:host=localhost;dbname=yii2_project',
            'username' => 'root',
            'password'  => 'root',
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
    ],
    'modules' => [
        'debug' => 'yii\debug\Module',
        'gii' => 'yii\gii\Module',
        'test' => 'app\modules\test\Module',

    ],
    'params' => [
        'admin_email' => 'baript@gmail.com',
        'salt' => 'a689839638e2243145ac9b2683cac9bd',
        'videochat' => [
            'address' => 'ws://0.0.0.0:8090',
            'certificate' => '',
            'key' => '',
        ],
    ]
];
// openai api key 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