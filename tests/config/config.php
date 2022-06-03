<?php
/**
 * Application configuration shared by all test types
 */
return \yii\helpers\ArrayHelper::merge([
    'language' => 'en',
    'bootstrap' => ['log'],
    'components' => [
        'request'=> ['enableCsrfValidation'=>false,],
        'mailer' => ['useFileTransport' => true,],
    ],
    'params' => ['hostInfo' => 'http://localhost:8081',]
], (file_exists(__DIR__ . '/config-local.php') ? (require __DIR__ . '/config-local.php') : []));
