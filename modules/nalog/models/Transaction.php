<?php

namespace app\modules\nalog\models;

use app\modules\common\components\validators\ListValidator;
use app\modules\common\helpers\DateHelper;
use app\modules\user\models\User;
use Yii;

/**
 * This is the model class for table "nalog_transaction".
 *
 * @property int $id
 * @property int|null $type
 * @property int|null $user_id
 * @property int|null $source_id
 * @property string|null $date
 * @property float|null $amount
 * @property string|null $currency
 * @property string $description
 *
 * @property Source $source
 * @property User $user
 */
class Transaction extends \yii\db\ActiveRecord
{
    const TYPE_INCOME = 0;
    const TYPE_OUTCOME = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%nalog_transaction}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'source_id', 'currency'], ListValidator::class],
            [['amount'], 'number'],
            ['description', 'safe'],
            [['date'], 'date', 'format' => DateHelper::DATE_SAVING_FORMAT],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('modules/nalog', 'ID'),
            'type' => Yii::t('modules/nalog', 'Type'),
            'user_id' => Yii::t('modules/nalog', 'User ID'),
            'source_id' => Yii::t('modules/nalog', 'Source ID'),
            'date' => Yii::t('modules/nalog', 'Date'),
            'amount' => Yii::t('modules/nalog', 'Amount'),
            'currency' => Yii::t('modules/nalog', 'Currency'),
            'description' => Yii::t('modules/nalog', 'Description'),
        ];
    }

    /**
     * Gets query for [[Source]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(Source::className(), ['id' => 'source_id']);
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
    public static function typeList()
    {
        return [
            static::TYPE_INCOME => Yii::t('modules/nalog', 'Income'),
            static::TYPE_OUTCOME => Yii::t('modules/nalog', 'Outcome'),
        ];
    }

    /**
     * @return array
     */
    public function sourceList()
    {
        return Source::find()->andWhere(['user_id' => $this->user_id])->indexBy('id')->select('name')->column();
    }

    /**
     * @return array
     */
    public static function currencyList()
    {
        return \app\modules\common\helpers\GeoHelper::currencyList();
    }
}
