<?php

namespace app\modules\user\models;

use app\modules\common\components\behaviors\EncryptBehavior;
use Yii;
use yii\authclient\BaseOAuth;
use yii\authclient\Collection;

/**
 * This is the model class for table "user_auth".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $name
 * @property string $service_id
 * @property integer $created_at
 * @property string $data
 *
 * @property User $user
 */
class Auth extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_auth}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'user_id' => Yii::t('user', 'User ID'),
            'name' => Yii::t('user', 'Name'),
            'service_id' => Yii::t('user', 'Service ID'),
            'created_at' => Yii::t('user', 'Created At'),
            'data' => Yii::t('user', 'Data'),
        ];
    }
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'encrypt' => ['class' => EncryptBehavior::class, 'attributes' => ['data']],
        ];
    }

    /**
     * @param BaseOAuth $client
     * @return self
     */
    public static function clientInstance(Token $token)
    {
        $attributes = [
            'name' => $token->type,
            'service_id' => $token->owner
        ];
        /**
         * @var self $model
         */
        $user = User::current() ? : new User(['status' => User::STATUS_ACTIVE]);
        if (!$model = static::findOne($attributes)) {
            $model = new self(array_merge($attributes, ['created_at' => time()]));
            $model->data = $token->toArray();
            $model->save(false);
            if ($user->isNewRecord) {
                $user->email = $user->username = $model->name . $model->id;
                $user->save(false);
            }
        }
        if ($user->id) {
            $model->updateAttributes(['user_id' => $user->id]);
            $model->populateRelation('user', $user);
        }
        return $model;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return static::hasOne(User::className(), ['id'=> 'user_id']);
    }

    /**
     * @param null $user_id
     * @param string $type
     * @return Token
     */
    public static function token($user_id = null, $type = Token::TYPE_GOOGLE)
    {
        return Token::fromArray(Auth::findOne(['user_id' => $user_id ?? \Yii::$app->user->id, 'name' => $type]));
    }
}
