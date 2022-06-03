<?php
/**
 * Application configuration shared by all test types
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
return [
    'components' => [
        'assetManager' => [
            'appendTimestamp' => false,
            'linkAssets' => false,
            'hashCallback' => function ($path) {
                if (strpos($path, Yii::getAlias('@app/web')) === 0) {
                    return '..' . str_replace(Yii::getAlias('@app/web'), '', $path);
                }
                return '../html/assets/'. str_replace(Yii::getAlias('@app'), '', $path);
            },
        ]
    ],
    'params' => [
        'auth' => ['username' => 'test@test.test', 'password' => 'asd123!'],
        'admin_auth' => ['username' => 'admin@test.test', 'password' => '123123'],
    ]
];
