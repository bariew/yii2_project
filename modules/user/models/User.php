<?php
/**
 * User class file.
 */

namespace app\modules\user\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;
 
/**
 * Application user model.
 * 
 *
 * 
 * @property integer $id
 * @property string $username
 * @property string $company_name
 * @property string $auth_key
 * @property string $api_key
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 *
 * @property Auth[] $auths
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 10;

    const SCENARIO_ROOT = 'root';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => static::STATUS_ACTIVE],
            ['status', 'in', 'range' => array_keys($this->statusList()), 'on' => static::SCENARIO_ROOT],
            ['username', 'filter', 'filter' => 'trim'],
            [['email', 'username', 'password'], 'required'],
            [['email', 'username', 'api_key'], 'unique'],
            [['username', 'company_name'], 'string', 'min' => 2, 'max' => 255],
            'password' => ['password', 'string', 'min' => 5, 'max' => 255], /** @see Login::rules() */
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'        => Yii::t('user', 'Email'),
            'username'     => Yii::t('user', 'Name'),
            'company_name' => Yii::t('user', 'Company name'),
            'auth_key'     => Yii::t('user', 'Auth key'),
            'api_key'      => Yii::t('user', 'Api key'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert && !$this->api_key) {
            $this->generateApiKey();
        }
        if ($insert && !$this->auth_key) {
            $this->generateAuthKey();
        }
        if ($insert || $this->isAttributeChanged('password')) {
            $this->password = $this->generatePassword($this['password']);
        }
        return true;
    }

    /**
     * @return IdentityInterface|null|static
     */
    public static function current()
    {
        return Yii::$app->user->identity;
    }

    /**
     * gets all available user status list
     * @return array statuses
     */
    public static function statusList()
    {
        return [
            static::STATUS_INACTIVE => Yii::t('user', 'Deactivated'),
            static::STATUS_ACTIVE   => Yii::t('user', 'Active')
        ];
    }

    /**
     * Gets model readabe status name.
     * @return string
     */
    public function getStatusName()
    {
        return static::statusList()[$this->status];
    }

    /**
     * Activates user.
     * @return boolean
     */
    public function activate()
    {
        return $this->updateAttributes([
            'status' => static::STATUS_ACTIVE,
            'auth_key' => null
        ]);
    }
    
    /**
     * 
     * @return boolean
     */
    public function isActive()
    {
        return $this->status == static::STATUS_ACTIVE;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($auth_key, $type = NULL)
    {
        static::findOne(compact('auth_key'));
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key == $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password original password.
     * @return string hashed password.
     */
    public function generatePassword($password)
    {
        return Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        return $this->auth_key = md5(Yii::$app->security->generateRandomKey());
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function generateApiKey()
    {
        return $this->api_key = md5(Yii::$app->security->generateRandomKey());
    }

    // RELATIONS

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuths()
    {
        return $this->hasMany(Auth::className(), ['user_id' => 'id'])->indexBy('service_id');
    }
}
