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
 * @property string|null $name
 * @property string|null $description
 * @property string $currency
 *
 * @property Transaction[] $nalogTransactions
 * @property User $user
 */
class Source extends \yii\db\ActiveRecord
{
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
            'name' => Yii::t('modules/nalog', 'Name'),
            'description' => Yii::t('modules/nalog', 'Description'),
            'currency' => Yii::t('modules/nalog', 'Default Currency'),
        ];
    }

    /**
     * Gets query for [[NalogTransactions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNalogTransactions()
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

    /**
     * @return array
     */
    public static function currencyList()
    {
        return \app\modules\common\helpers\GeoHelper::currencyList();
    }

    /**
     * @return Source|null
     */
    public static function last()
    {
        return static::findOne(
            Transaction::find()->andWhere(['user_id' => User::current()->id])
            ->orderBy(['date' => SORT_DESC])->select('source_id')->scalar()
        );
    }
}
