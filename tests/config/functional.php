<?php
$_SERVER['SCRIPT_FILENAME'] = YII_TEST_ENTRY_FILE;
$_SERVER['SCRIPT_NAME'] = YII_TEST_ENTRY_URL;
$mainConfig = require(__DIR__ . '/../../config/web.php');
$mainConfig['bootstrap'] = ['log'];
unset($mainConfig['components']['session']['cookieParams'], $mainConfig['user']['identityCookie']);
/**
 * Application configuration for functional tests
 */
return yii\helpers\ArrayHelper::merge(
    $mainConfig,
    require(__DIR__ . '/config.php'),
    [
        'components' => [
            'request' => ['enableCsrfValidation' => false,],
            'urlManager' => ['baseUrl' => 'http://localhost:8081',],
        ],
    ]
);
