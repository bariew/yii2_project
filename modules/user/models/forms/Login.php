<?php
/**
 * LoginForm class file.
 */

namespace app\modules\user\models\forms;

use Yii;
use yii\base\Model;
use app\modules\user\models\User;

/**
 * For user login form.
 * 
 *
 */
class Login extends Model
{
    const SCENARIO_PASSWORD_FORGOT = 'password_forgot';
    const SCENARIO_PASSWORD_RESET = 'password_reset';
    const EVENT_PASSWORD_FORGOT = 'passwordForgot';
    public $email;
    public $password;
    public $password_repeat;
    public $rememberMe = true;
    /**
     * @var User
     */
    public $user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'email'],
            [['email', 'password'], 'required', 'except' => [static::SCENARIO_PASSWORD_FORGOT]],
            ['rememberMe', 'boolean'],
            ['password', 'compare', 'on' => [static::SCENARIO_PASSWORD_RESET]],
            [['password_repeat'], 'safe'],
            'password' => (new User())->rules()['password'],
            ['password', 'validatePassword', 'on' => [static::SCENARIO_DEFAULT]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'active'    => Yii::t('user', 'Active'),
            'email'    => Yii::t('user', 'Email'),
            'rememberMe'    => Yii::t('user', 'Remember me'),
            'password'    => Yii::t('user', 'Password'),
            'password_repeat'    => Yii::t('user', 'Repeat Password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        parent::afterValidate();
        if ($this->password && $this->user) {
            $this->user->password = $this->password;
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', 'Incorrect email or password.');
            }
        }
    }

    /**
     * @param null $date
     * @return bool|string
     */
    public function resetCode($date = null)
    {
        if (!$user = $this->getUser()) {
            return false;
        }
        $date = $date ? : gmdate('Y-m-d');
        return sha1(Yii::$app->params['salt'].$date.$this->user->id);
    }

    /**
     * Logs in a user using the provided email and password.
     * @param bool $validate
     * @return boolean whether the user is logged in successfully
     */
    public function login($validate = true)
    {
        if (!$validate || $this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->user = ($this->user !== false )
            ? $this->user
            : User::findOne(['email' => $this->email]);
    }
}
