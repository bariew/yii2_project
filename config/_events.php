<?php

use yii\base\Event;

// BASE
// add migration paths from modules
Event::on(\yii\console\Application::class, \yii\console\Application::EVENT_BEFORE_ACTION, function ($e) {
    if (Yii::$app->controller instanceof \yii\console\controllers\BaseMigrateController) {
        $controller = Yii::$app->controller; /** @var \yii\console\controllers\BaseMigrateController $controller */
        $controller->migrationPath = array_merge((array) $controller->migrationPath, array_map(function ($k) {
            return basename($k)."/migrations";
        }, \yii\helpers\FileHelper::findDirectories(Yii::getAlias('@app/modules'), ['recursive' => false])));
    }
});


// RBAC
Event::on(\yii\web\Controller::class, \yii\web\Application::EVENT_BEFORE_ACTION, function (Event $e) {
    \app\modules\rbac\components\EventHandlers::beforeActionAccess($e);
});
Event::on(\yii\web\Response::class, \yii\web\Response::EVENT_AFTER_PREPARE, function ($e) {
   // \app\modules\rbac\components\EventHandlers::responseAfterPrepare($e);
});

