<?php
/**
 * Application configuration shared by all test types
 */
return \yii\helpers\ArrayHelper::merge([
    'language' => 'en',
    'bootstrap' => ['log'],
    'controllerMap' => [
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::className(),
            //'fixtureDataPath' => '@tests/codeception/fixtures/data',
            'namespace' => 'tests\codeception\fixtures',
        ],
    ],
    'components' => [
        'mailer' => [
           // 'useFileTransport' => true,
        ],
       // 'log' => [],
    ],
    'params' => [
        'baseUrl' => 'http://localhost:8081',
    ]
], (file_exists(__DIR__ . '/config-local.php') ? (require __DIR__ . '/config-local.php') : []));
