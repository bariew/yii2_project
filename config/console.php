<?php
$mainConfig = require 'main.php';
$mainConfig['bootstrap'] = array_diff($mainConfig['bootstrap'], ['debug']);
return \yii\helpers\ArrayHelper::merge([
    'id' => 'console',
    'controllerNamespace' => 'app\modules\common\controllers',
    'components' => [
        'urlManager' => [
            'hostInfo' => $mainConfig['params']['hostInfo'],
            'baseUrl' => '/',
	],
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => array_filter(array_map(function($v) {
                return (isset($v['class']) && strpos($v['class'], 'app') === 0 ? '@'.str_replace(['\\','Module'], ['/', 'migrations'], $v['class']) : null);
            }, $mainConfig['modules'])),
        ]
    ]
], $mainConfig);
