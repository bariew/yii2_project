<?php

namespace app\modules\nalog\models;

use app\modules\common\components\validators\ListValidator;
use app\modules\common\helpers\DateHelper;
use app\modules\user\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "nalog_transaction".
 *
 * @property int $id
 * @property int|null $tax_type
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
    const TAX_TYPE_USN_INCOME = 1;
    const TAX_TYPE_USN_INCOME_OUTCOME = 2;
    const TAX_TYPE_PATENT= 3;

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
            [['source_id', 'currency', 'tax_type'], ListValidator::class],
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
            'tax_type' => Yii::t('modules/nalog', 'Tax Type'),
            'user_id' => Yii::t('modules/nalog', 'User ID'),
            'source_id' => Yii::t('modules/nalog', 'Source ID'),
            'date' => Yii::t('modules/nalog', 'Date'),
            'amount' => Yii::t('modules/nalog', 'Amount'),
            'currency' => Yii::t('modules/nalog', 'Currency'),
            'description' => Yii::t('modules/nalog', 'Description'),
        ];
    }


    // LISTS & GETTERS

    /**
     * @return array
     */
    public static function taxTypeList()
    {
        return [
            static::TAX_TYPE_USN_INCOME => Yii::t('modules/nalog', 'USN income'),
            static::TAX_TYPE_USN_INCOME_OUTCOME => Yii::t('modules/nalog', 'USN income/outcome'),
            static::TAX_TYPE_PATENT => Yii::t('modules/nalog', 'Patent'),
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
    /**
     * @return array
     */
    public static function typeList()
    {
        return Source::typeList();
    }

    /**
     * @return int|null
     */
    public function getType()
    {
        return $this->source ? $this->source->type : null;
    }

    /**
     * @return static|ActiveRecord
     */
    public static function last()
    {
        return static::find()->andWhere(['user_id' => User::current()->id])->orderBy(['id' => SORT_DESC])->one();
    }


    // RELATIONS

    /**
     * Gets query for [[Source]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(Source::className(), ['id' => 'source_id'])->alias('source');
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->alias('user');
    }
}
