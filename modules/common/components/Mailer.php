<?php
/**
 * Mailer class file.
 */

namespace app\modules\common\components;

use app\modules\page\models\ContactForm;
use app\modules\user\models\LoginForm;
use app\modules\user\models\RegisterForm;
use Yii;
use yii\base\Event;
use yii\helpers\Url;

/**
 * Description:
 */
class Mailer
{
    const VIEW_CONTACT = 'view_contact';
    const VIEW_PASSWORD_FORGOT = 'view_password_forgot';
    const VIEW_REGISTRATION_COMPLETE = 'view_registration_complete';

    /**
     * @param Event $event
     * @return bool
     */
    public static function contactFormSend(Event $event)
    {
        /** @var ContactForm $model */
        $model = $event->sender;
        return static::send(
            static::VIEW_CONTACT,
            ['model' => $model],
            [Yii::$app->params['admin_email'] => Yii::$app->name],
            Yii::t('app', 'feedback_from_{email}', ['email' => $model->email])
        );
    }

    /**
     * @param Event $event
     * @return bool
     */
    public static function passwordForgot(Event $event)
    {
        /** @var LoginForm $model */
        $model = $event->sender;
        return static::send(static::VIEW_PASSWORD_FORGOT, ['model' => $model], $model->email);
    }

    /**
     * @param Event $event
     * @return bool
     */
    public static function registrationComplete(Event $event)
    {
        /** @var RegisterForm $model */
        $model = $event->sender;
        return static::send(static::VIEW_REGISTRATION_COMPLETE, ['model' => $model], $model->email);
    }

    /**
     * @param $view
     * @param $params
     * @param $to
     * @param null $subject
     * @return bool
     */
    private static function send($view, $params, $to = null, $subject = null)
    {
        /** @var \yii\swiftmailer\Mailer|\sweelix\postmark\Mailer $mailer */
        $mailer = Yii::$app->mailer;
        $to = $to ? : Yii::$app->params['admin_email'];
        return $mailer->compose($view, $params)
            ->setFrom(Yii::$app->params['admin_email'])
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }
}