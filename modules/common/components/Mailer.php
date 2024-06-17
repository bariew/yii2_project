<?php
/**
 * Mailer class file.
 */

namespace app\modules\common\components;

use Yii;

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
     * @param null $subject
     * @param $params
     * @param $to
     * @return bool
     */
    public static function send($view, $subject, $params = [], $to = null)
    {
        /** @var \yii\symfonymailer\Mailer $mailer */
        $mailer = Yii::$app->mailer;
        $to = $to ? : Yii::$app->params['admin_email'];
        return $mailer->compose($view, $params)
            ->setFrom(Yii::$app->params['admin_email'])
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }
}