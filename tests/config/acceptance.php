<?php
$mainConfig = require(__DIR__ . '/../../config/web.php');
$mainConfig['bootstrap'] = ['log'];
unset($mainConfig['components']['session']['cookieParams'], $mainConfig['user']['identityCookie']);
/**
 * Application configuration for acceptance tests
 */
return yii\helpers\ArrayHelper::merge($mainConfig, require(__DIR__ . '/config.php'));
