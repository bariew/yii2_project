<?php
$mainConfig = require 'main.php';
return \yii\helpers\ArrayHelper::merge($mainConfig, [
    'id' => 'web',
    'components' => [
        'errorHandler' => ['errorAction' => 'common/site/error',],
        'request'   => ['cookieValidationKey'   => 'someValidationKey'],
        'user' => [
            'identityClass' => 'app\\modules\\user\\models\\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/default/login']
        ],
    ]
]);