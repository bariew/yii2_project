<?php
/**
 * ContactForm class file.
 */

namespace app\modules\page\models;

use \Yii;


/**
 * It is used by the 'contact' action of 'SiteController'.
 */
class ContactForm extends \yii\base\Model 
{
    const EVENT_AFTER_SEND = 'afterContactFormSend';

    public $email;
    public $message;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            [['message'], 'required'],
            ['email', 'email'],
        ];
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('page', 'email'),
            'message' => Yii::t('page', 'message'),
        ];
    }

    /**
     * Sends an email
     * @return bool
     */
    public function send()
    {
        if (!$this->validate()) {
            return false;
        }
        $this->trigger(static::EVENT_AFTER_SEND);
        return true;
    }
}