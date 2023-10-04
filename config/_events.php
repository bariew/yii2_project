<?php

use yii\base\Event;

// BASE

Event::on(\yii\console\Application::class, \yii\console\Application::EVENT_BEFORE_ACTION, function ($e) {
    if (Yii::$app->controller instanceof \yii\console\controllers\BaseMigrateController) {// add migration paths from modules
        $controller = Yii::$app->controller; /** @var \yii\console\controllers\BaseMigrateController $controller */
        $controller->migrationPath = array_merge((array) $controller->migrationPath, array_map(function ($k) {
            return basename($k)."/migrations";
        }, \yii\helpers\FileHelper::findDirectories(Yii::getAlias('@app/modules'), ['recursive' => false])));
    }
});
Event::on(\yii\web\Controller::class, \yii\web\Controller::EVENT_AFTER_ACTION, function (\yii\base\ActionEvent $e) {
    if (Yii::$app->request->isGet && !Yii::$app->request->isAjax && Yii::$app->request->referrer
        && !in_array(Yii::$app->request->referrer, [Yii::$app->user->returnUrl, \yii\helpers\Url::current($_GET, true)])
        && !$e->action instanceof \yii\web\ErrorAction
        && !Yii::$app->controller instanceof \app\modules\user\controllers\DefaultController
    ) {
        Yii::$app->user->setReturnUrl(Yii::$app->request->referrer);
    }
});

// RBAC
Event::on(\yii\web\Controller::class, \yii\web\Application::EVENT_BEFORE_ACTION, function (Event $e) {
    \app\modules\rbac\components\EventHandlers::beforeActionAccess($e);
});
Event::on(\yii\web\Response::class, \yii\web\Response::EVENT_AFTER_PREPARE, function ($e) {
   // \app\modules\rbac\components\EventHandlers::responseAfterPrepare($e);
});

