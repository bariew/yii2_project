<?php
/**
 * Mailer class file.
 */

namespace app\modules\common\components;

use app\modules\page\models\ContactForm;
use app\modules\user\models\forms\Login;
use app\modules\user\models\forms\Register;
use Yii;
use yii\base\Event;

/**
 * Description:
 */
class Mailer
{
    const VIEW_CONTACT = 'view_contact';
    const VIEW_PASSWORD_FORGOT = 'view_password_forgot';
    const VIEW_REGISTRATION_COMPLETE = 'view_registration_complete';

    /**
     * @param $view
     * @param $params
     * @param $to
     * @param null $subject
     * @return bool
     */
    public static function send($view, $params, $to = null, $subject = null)
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