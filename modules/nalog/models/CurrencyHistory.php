<?php

namespace app\modules\nalog\models;

use Yii;

/**
 * This is the model class for table "{{%nalog_currency_history}}".
 *
 * @property string|null $date
 * @property float|null $usd
 */
class CurrencyHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%nalog_currency_history}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['usd'], 'number'],
            [['date'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'date' => Yii::t('modules/nalog', 'Date'),
            'usd' => Yii::t('modules/nalog', 'Usd'),
        ];
    }
}
