<?php

namespace app\modules\user\models;

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
            'id' => Yii::t('modules/user', 'ID'),
            'user_id' => Yii::t('modules/user', 'User ID'),
            'name' => Yii::t('modules/user', 'Name'),
            'service_id' => Yii::t('modules/user', 'Service ID'),
            'created_at' => Yii::t('modules/user', 'Created At'),
            'data' => Yii::t('modules/user', 'Data'),
        ];
    }

    /**
     * @param BaseOAuth $client
     * @return self
     */
    public static function clientInstance(BaseOAuth $client)
    {
        $attributes = [
            'name' => $client->getName(),
            'service_id' => $client->id
        ];
        /**
         * @var self $model
         */
        $user = User::current() ? : new User(['status' => User::STATUS_ACTIVE]);
        if (!$model = static::findOne($attributes)) {
            $model = new self(array_merge($attributes, [
                'created_at' => time(),
                'data' => json_encode($client->getUserAttributes())
            ]));
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
     * @return Collection
     */
    public static function clientCollection()
    {
        return Yii::$app->authClientCollection;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return static::hasOne(User::className(), ['id'=> 'user_id']);
    }
}
