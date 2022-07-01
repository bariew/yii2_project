<?php
/**
 * DateHelper class file
 */


namespace app\modules\common\helpers;

use kartik\datecontrol\DateControl;
use kartik\datetime\DateTimePicker;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class DateHelper
 * @package app\modules\common\helpers
 */
class DateHelper
{
    const DATE_SAVING_FORMAT = 'php:Y-m-d H:i:s';
    const DATE_DEFAULT_FORMAT = 'Y-m-d H:i:s';
    const MONTH_LAST_DAYS = [29,30,31];

    /**
     * @param string $diff
     * @param string $format
     * @param string $timezone
     * @return false|string
     * @throws \Exception
     */
    public static function now($diff = 'now', $format = self::DATE_DEFAULT_FORMAT, $timezone = 'UTC')
    {
        return (new \DateTime($diff, new \DateTimeZone($timezone)))->format($format);
    }

    /**
     * @param $date // gets UTC date and calculates current timezone same day datetime value for specific time, returns it in UTC for saving
     * @param $h integer hour
     * @param $m integer minutes
     * @param $s integer seconds
     * @param null $timezone
     * @return string
     * @throws \Exception
     */
    public static function dayAtTime($date, $h, $m, $s, $timezone = null)
    {
        return $date
            ? (new \DateTime($date, new \DateTimeZone('UTC')))
                ->setTimezone(new \DateTimeZone($timezone ?? Yii::$app->timeZone))
                ->setTime($h, $m, $s)
                ->setTimeZone(new \DateTimeZone('UTC'))
                ->format(str_replace('php:', '', static::DATE_SAVING_FORMAT))
            : $date;
    }

    /**
     * @param $date
     * @param null $timezone
     * @return string
     */
    public static function dayStart($date, $timezone = null)
    {
        return static::dayAtTime($date, 00, 00, 00, $timezone);
    }

    /**
     * @param $date
     * @param null $timezone
     * @return string
     */
    public static function dayEnd($date, $timezone = null)
    {
        return static::dayAtTime($date, 23, 59, 59, $timezone);
    }

    /**
     * @param array $options
     * @param null $defaultDate
     * @return array
     * @throws \yii\db\Exception
     */
    public static function dateControlOptions($options = [], $defaultDate = null)
    {
        $result = \yii\helpers\ArrayHelper::merge([
            'type' => DateControl::FORMAT_DATE, // these 3 options are for nondefault Kartik DatePicker
//            'autoWidget' => false,
//            'widgetClass' => DatePickerMobile::class,
            'displayFormat' => 'dd.MM.yyyy',
            'saveFormat' => DateHelper::DATE_SAVING_FORMAT,
            'displayTimezone' => Yii::$app->timeZone,
            'saveTimezone' => 'UTC',
            'widgetOptions' => [
                'options' => ['autocomplete' => 'off'],
                'type' => (HtmlHelper::isRtl() ? DateTimePicker::TYPE_COMPONENT_APPEND : DateTimePicker::TYPE_COMPONENT_PREPEND),
                'pluginOptions' => [ // these options are for Kartik DatePicker
                    'autoclose' => true,
                    //'startDate' => date('Y-m-d'),
                    //'locale'=>['format' => 'Y-m-d'],
                    // 'rtl' => HtmlHelper::isRtl(),
                    'minView' => 2, // exclude hours and minutes
                    'initialDate' => ($defaultDate ? : false),
                    'language' => \Yii::$app->language,
                    'todayHighlight' => false,
                   // 'weekStart' => \app\modules\company\models\Company::current()->starting_weekday,
                    'viewSelect' => 2, //insert input value after day select
                ],
                'pluginEvents' => [
                    // 'changeDay' => new JsExpression("function (e) { console.log(e); }")
                ]
            ]
        ], $options);
        return $result;
    }

    /**
     * @param array $options
     * @return array
     */
    public static function dateControlSearchOptions($options = [])
    {
        $result = static::dateControlOptions();
        unset($result['saveFormat'], $result['widgetOptions']['pluginOptions']['startDate']);
        return \yii\helpers\ArrayHelper::merge($result, $options);
    }

    /**
     * Transforms default site data format to sql search format
     * @param ActiveRecord $model
     * @param $attribute
     * @param bool $isTimestamp
     * @return array
     */
    public static function dateCondition(ActiveRecord $model, $attribute, $isTimestamp = false)
    {
        $date = $model->getAttribute(preg_replace('/^(.*\.)?(.*)$/', '$2', $attribute));// in case it's a search prefixed attribute like 'p.name'
        if (!preg_match('/(\d{4}-\d{2}-\d{2}) - (\d{4}-\d{2}-\d{2})/', $date, $matches)) {
            return ['between', $attribute, null, null];
        }
        $attribute = $isTimestamp ? "FROM_UNIXTIME({$attribute})" : $attribute;
        return ['between', "DATE_FORMAT({$attribute}, '%Y-%m-%d')", $matches[1], $matches[2]];
    }


    /**
     * Calculates a rounded difference between two dates
     * @example DateHelper::diff('2020-12-23 05:01', '2020-12-25', 24*60*60) // -1 days
     * @param string $from
     * @param string $to
     * @param float|int $divider
     * @return false|float
     */
    public static function diff($to, $from = null, $divider = 24*60*60)
    {
        if (!$to) {
            return null;
        }
        $from = $from ?? date('Y-m-d');
        return floor(strtotime($to)/$divider) - floor(strtotime($from)/$divider);
    }

    /**
     * @param $date
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function diffReadable($date)
    {
        if (!$date) {
            return '';
        }
        $diff = static::diff($date);
        $prepend = $diff > 0
            ? Yii::t('date', 'in {number} days', ['number' => $diff])
            : Yii::t('date', '{number} days ago', ['number' => $diff]);
        return Yii::$app->formatter->asDate($date) . " ($prepend)";
    }

    /**
     * @param int $start
     * @param string $format
     * @return string[]
     * @throws \yii\base\InvalidConfigException
     */
    public static function weekdaysList($start = 1, $format = 'eeee')
    {
        $result = [
            1 => Yii::$app->formatter->asDate('2020-08-17', $format), // Monday
            2 => Yii::$app->formatter->asDate('2020-08-18', $format),
            3 => Yii::$app->formatter->asDate('2020-08-19', $format),
            4 => Yii::$app->formatter->asDate('2020-08-20', $format),
            5 => Yii::$app->formatter->asDate('2020-08-21', $format),
            6 => Yii::$app->formatter->asDate('2020-08-22', $format),
            7 => Yii::$app->formatter->asDate('2020-08-23', $format),
        ];
        return array_slice($result, $start-1, null, true)+array_slice($result, 0, $start-1, true);
    }

    const MONTHDAY_LAST = 0;

    /**
     * @return array
     */
    public static function monthDaysList()
    {
        return array_combine(range(1,31), range(1,31)) + [static::MONTHDAY_LAST => Yii::t('common', 'Last Day')];
    }

    /**
     * @param $start
     * @param $end
     * @param int $weekStart
     * @return array
     */
    public static function workingDays($start, $end, $weekStart = 1)
    {
        $result = [];
        if (!$start || !$end) {
            return $result;
        }
        $weekend = $weekStart == 7 ? [5, 6] : [6,7];
        $diff = static::diff($end, $start)+1;
        while ($diff) {
            $diff--;
            $today = static::now($start . "+{$diff}day");
            if (in_array(date('N', strtotime($today)), $weekend)) {
                continue;
            }
            $result[] = Yii::$app->getFormatter()->asDate($today);
        }
        return array_reverse($result);
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function dayNames()
    {
        return [
            Yii::$app->formatter->asDate(strtotime('-1day')) => Yii::t('common', 'yesterday'),
            Yii::$app->formatter->asDate(strtotime('now')) => Yii::t('common', 'today'),
            Yii::$app->formatter->asDate(strtotime('+1day')) => Yii::t('common', 'tomorrow'),
        ];
    }

    /**
     * @return bool
     */
    public static function isLastMonthDay()
    {
        return static::now('now', 'j') == static::now('now', 't');
    }
}