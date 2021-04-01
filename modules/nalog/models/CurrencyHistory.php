<?php

namespace app\modules\nalog\models;

use app\modules\common\helpers\DbHelper;
use app\modules\nalog\components\Cbr;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "{{%nalog_currency_history}}".
 *
 * @property string|null $date
 * @property float|null $usd
 */
class CurrencyHistory extends \yii\db\ActiveRecord
{
    const CURRENCIES = ['USD', 'EUR', 'CNY', 'GBP'];
    const START_DATE = '2000-01-01';

    public $select;

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
            [['date', 'select'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'date' => Yii::t('modules/nalog', 'Date'),
            'select' => Yii::t('modules/nalog', 'Select'),
        ];
    }

    /**
     * @param $currency
     */
    public static function importCurrency($currency)
    {
        $data = Cbr::instance()->GetCursDynamic(
            static::importStartDate($currency),
            date('Y-m-d'),
            Cbr::currencies()[$currency]['Vcode']
        )['ValuteData']['ValuteCursDynamic'];
        return DbHelper::insertUpdate(static::tableName(), array_map(function ($v) {
            return [substr($v['CursDate'], 0, 10), $v['Vcurs']];
        }, $data), ['date', strtolower($currency)]);
    }

    /**
     * @param $column
     * @return false|string
     */
    public static function importStartDate($column)
    {
        $start = static::find()->andWhere(['<>', strtolower($column), ''])->max('date');
        return $start ? date('Y-m-d', strtotime($start.'+1day')) : static::START_DATE;
    }

    /**
     * @return bool
     */
    public static function importDragMetals()
    {
        $data = Cbr::instance()->DragMetDynamic(static::importStartDate('gold'), date('Y-m-d'))['DragMetall']['DrgMet'];
        $result = [];
        foreach ($data as $v) {
            $date = substr($v['DateMet'], 0, 10);
            $result[$date] = array_merge(
                ($result[$date] ?? array_fill_keys(array_merge(['date'], Cbr::dragMetals()), '')),
                ['date' => $date, Cbr::dragMetals()[$v['CodMet']] => $v['price']]
            );
        }
        return DbHelper::insertUpdate(static::tableName(), $result, array_merge(['date'], Cbr::dragMetals()));
    }

    /**
     * @param $data
     * @return \yii\db\ActiveQuery
     */
    public function search($data)
    {
        $this->load($data);
        $group = "DATE_FORMAT(date, '%Y')";
        $query = static::find();
        if ($this->date) {
            list($from, $to) = explode(' - ', $this->date);
            $diff = (strtotime($to)-strtotime($from))/(60*60*24);
            if ($diff < 62) {
                $group = "DATE_FORMAT(date, '%Y-%m-%d')";
            } else if ($diff < 1500) {
                $group = "DATE_FORMAT(date, '%Y-%m')";
            }
            $query->andFilterWhere(['between', 'date', $from, $to]);
        }
        $select = array_map(function ($v) {
            return new Expression("ROUND(AVG({$v}), 0)");
        }, array_combine($this->attributes(), $this->attributes()));
        if ($this->select) {
            $select = array_intersect_key($select, array_flip($this->select));
        }
        $query->select(array_merge($select, ['date' => new Expression($group)]));
        return $query->groupBy('date')->indexBy('date');
    }
}
