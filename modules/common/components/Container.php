<?php
/**
 * Container class file
 */

namespace app\modules\common\components;

use Hashids\Hashids;
use yii\mail\MailerInterface;
use yii\queue\Queue;
use yii\swiftmailer\Mailer;

/**
 * Class Container
 * @package app\modules\common\components
 */
class Container
{
    const PARAM_ADMIN_EMAIL = 'adminEmail';

    /**
     * @return Queue|object
     */
    public static function queue()
    {
        return \Yii::$app->queue;
    }

    /**
     * @return \yii\mail\MailerInterface|\yashop\ses\Mailer
     */
    public static function mailer()
    {
        return \Yii::$app->mailer;
    }

    /**
     * @return Hashids
     */
    public static function hashids()
    {
        return  new Hashids(\Yii::$app->params['salt'], 10);
    }
}