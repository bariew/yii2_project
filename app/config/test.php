<?php
//require(dirname(dirname(__DIR__)).'/tests/codeception/_bootstrap.php');
$mainConfig = require(dirname(__FILE__) . '/web.php');
return \yii\helpers\ArrayHelper::merge($mainConfig, array(
    "components" => array(
        "request"   => [
            'enableCsrfValidation' => false,
            "cookieValidationKey"   => "someValidationKey"
        ],
        'db'=>array(
            'dsn'=>$mainConfig['components']['db']['dsn'].'_test',
        ),
    ),
),
    require(dirname(__DIR__).'/tests/config/config.php')
);
