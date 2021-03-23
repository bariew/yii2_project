<?php
/**
 * RegisterForm class file.
 */

namespace app\modules\user\models;
 
/**
 * Form for user registration.
 * 
 *
 */
class RegisterForm extends User
{
    const EVENT_AFTER_COMPLETE = 'afterComplete';
    public $password_repeat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email'], 'unique'],
            ['email', 'email'],
            [['email', 'username', 'password', 'password_repeat'], 'required'],
            [['username', 'password', 'password_repeat'], 'string', 'min' => 2, 'max' => 255],
            ['password', 'compare'],
        ];
    }

    /**
     * @return bool|string
     */
    public function confirmationCode()
    {
        return (new LoginForm(['email' => $this->email]))->resetCode(1);
    }
}
