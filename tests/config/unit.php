<?php
/**
 * Application configuration for unit tests
 */
$result = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../config/web.php'),
    require(__DIR__ . '/config.php')
);
$result['bootstrap'] = [];
return $result;