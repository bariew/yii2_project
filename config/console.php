<?php
$mainConfig = require 'main.php';
$mainConfig['bootstrap'] = array_diff($mainConfig['bootstrap'], ['debug']);
return \yii\helpers\ArrayHelper::merge([
    'id' => 'console',
    'controllerNamespace' => 'app\modules\common\controllers',
    'components' => [
        'urlManager' => [
            'hostInfo' => $mainConfig['params']['hostInfo'],
            'scriptUrl' => $mainConfig['params']['hostInfo'],
            'baseUrl' => $mainConfig['params']['hostInfo'],
        ],
    ],
], $mainConfig);