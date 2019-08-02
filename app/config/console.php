<?php
$mainConfig = require __DIR__ . '/web.php';
return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'extensions'=> require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
    'name' => $mainConfig['name'],
    'timeZone' => $mainConfig['timeZone'],
    'language' => $mainConfig['language'],
    "bootstrap" => array('log'),
    'aliases' => $mainConfig['aliases'],
    'components' => [
        'db' => $mainConfig['components']['db'],
        'authManager' => $mainConfig['components']['authManager'],
        'log' => $mainConfig['components']['log'],
        'cache' => $mainConfig['components']['cache'],
        'i18n' => $mainConfig['components']['i18n'],
        'mailer' => $mainConfig['components']['mailer'],
        'urlManager' => array_merge($mainConfig['components']['urlManager'], [
            'baseUrl' => $mainConfig['params']['baseUrl'],
            'hostInfo' => $mainConfig['params']['baseUrl'],
        ]),
    ],
    'controllerMap' => [
        'db' => 'dizews\dbConsole\DbController',
    ],
    'modules' => $mainConfig['modules'],
    'params' => $mainConfig['params'],
];
