<?php

namespace app\modules\nalog\models;

use app\modules\common\components\validators\ListValidator;
use app\modules\user\models\User;
use Yii;

/**
 * This is the model class for table "nalog_source".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $type
 * @property string|null $name
 * @property string|null $description
 * @property string $currency
 *
 * @property Transaction[] $nalogTransactions
 * @property User $user
 */
class Source extends \yii\db\ActiveRecord
{
    const TYPE_INCOME = 0;
    const TYPE_OUTCOME = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%nalog_source}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['currency'], ListValidator::class],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('modules/nalog', 'ID'),
            'user_id' => Yii::t('modules/nalog', 'User ID'),
            'type' => Yii::t('modules/nalog', 'Type'),
            'name' => Yii::t('modules/nalog', 'Name'),
            'description' => Yii::t('modules/nalog', 'Description'),
            'currency' => Yii::t('modules/nalog', 'Default Currency'),
        ];
    }


    // LISTS & GETTERS

    /**
     * @return array
     */
    public static function currencyList()
    {
        return \app\modules\common\helpers\GeoHelper::currencyList();
    }

    /**
     * @return array
     */
    public static function typeList()
    {
        return [
            static::TYPE_INCOME => Yii::t('modules/nalog', 'Income'),
            static::TYPE_OUTCOME => Yii::t('modules/nalog', 'Outcome'),
        ];
    }


    // RELATIONS

    /**
     * Gets query for [[NalogTransactions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['source_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
