<?php

use app\components\Mailer;
use app\modules\page\models\ContactForm;
use yii\base\Event;
use app\modules\user\models\LoginForm;
use app\modules\user\models\RegisterForm;

// BASE
// add migration paths from modules
Event::on(\yii\console\Application::class, \yii\console\Application::EVENT_BEFORE_ACTION, function ($e) {
    if (Yii::$app->controller instanceof \yii\console\controllers\BaseMigrateController) {
        $controller = Yii::$app->controller; /** @var \yii\console\controllers\BaseMigrateController $controller */
        $controller->migrationPath = array_merge((array) $controller->migrationPath, array_map(function ($k) {
            return Yii::$app->getModule($k)->basePath . '/migrations';
        }, array_keys(Yii::$app->modules)));
    }
});
// add admin access to controllers with no access filter
Event::on(\yii\web\Application::class, \yii\web\Application::EVENT_BEFORE_ACTION, function (Event $e) {
    if (!Yii::$app->controller->getBehavior('access')) {
        Yii::$app->controller->attachBehavior('access', Yii::createObject([
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                ['allow' => true, 'roles' => ['admin']]
            ]
        ]));
    }
});


// EMAILS
Event::on(ContactForm::class, ContactForm::EVENT_AFTER_SEND, function($e){ Mailer::contactFormSend($e);});
Event::on(LoginForm::class, LoginForm::EVENT_PASSWORD_FORGOT, function($e){ Mailer::passwordForgot($e);});
Event::on(RegisterForm::class, RegisterForm::EVENT_AFTER_COMPLETE, function($e){ Mailer::registrationComplete($e);});

// LOGS // after emails and other events switching the sender
Event::on(\yii\web\User::class, \yii\web\User::EVENT_AFTER_LOGIN, function (Event $e) {
    $e->sender = Yii::$app->user->identity;
    \app\modules\log\components\EventHandler::common($e, ['ip' => Yii::$app->request->getUserIP(), 'user_agent' => Yii::$app->request->getUserAgent()]);
});
Event::on(ContactForm::class, ContactForm::EVENT_AFTER_SEND, function ($e) {
    $e->sender = Yii::$app->user->identity;
    \app\modules\log\components\EventHandler::common($e, ['ip' => Yii::$app->request->getUserIP(), 'user_agent' => Yii::$app->request->getUserAgent()]);
});
