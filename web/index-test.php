<?php
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../tests/config/config-local.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
$config = \yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../config/web.php'),
    require(__DIR__ . '/../tests/config/config.php')
);
unset($config['components']['session']['cookieParams'], $config['components']['user']['identityCookie']);
(new yii\web\Application($config))->run();