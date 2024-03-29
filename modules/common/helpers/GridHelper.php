<?php
/**
 * GridHelper class file.
 */

namespace app\modules\common\helpers;
use kartik\daterange\DateRangePicker;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\jui\DatePicker;
use Yii;
use yii\base\Model;

/**
 * Helper for GridView, GridList fields display.
 *
 */
class GridHelper
{
    /**
     * @var array model attribute lists values caching
     */
    private static $lists = [];

    /**
     * Gets sum for GridList model attribute
     * @param $dataProvider
     * @param $attributes
     * @return int
     */
    public static function columnSum($dataProvider, $attributes)
    {
        $result = 0;
        foreach ($dataProvider->models as $model) {
            foreach ((array) $attributes as $attribute) {
                $result += $model[$attribute];
            }
        }
        return $result;
    }

    /**
     * Gets model method name returning available values list for the attribute.
     * @param $attribute
     * @return string
     */
    public static function listName($attribute)
    {
        return lcfirst(Inflector::camelize(str_replace('_id', '', $attribute).'List'));
    }

    /**
     * Renders Grid column for list value
     * @param ActiveRecord|bool $model
     * @param $attribute
     * @param array $options
     * @return array
     */
    public static function listFormat($model, $attribute, $options = [])
    {
        $list = static::listFunction($model, $attribute);
        return array_merge([
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => !$model->isNewRecord
                ? implode(', ', array_intersect_key($list, array_flip((array) $model->$attribute)))
                : function ($data) use ($attribute) {
                    return @static::listFunction($data, $attribute)[$data->$attribute];
                },
            'filter' => $list ? : null,
            'visible' => $model->isAttributeSafe($attribute),
        ], $options);
    }

    /**
     * @param $model
     * @param $attribute
     * @param array $options
     * @return array
     */
    public static function kartikListFormat($model, $attribute, $options = [])
    {
        return \yii\helpers\ArrayHelper::merge(static::listFormat($model, $attribute), [
            'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
            'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true], 'options' => ['prompt' => ''],],
        ], $options);
    }

    /**
     * @param $model
     * @param $attribute
     * @return mixed
     */
    private static function listFunction($model, $attribute)
    {
        $method = static::listName($attribute);
        $key = get_class($model) . $attribute;
        return static::$lists[$key] = method_exists(get_class($model), $method) && isset(static::$lists[$key])
            ? static::$lists[$key] : $model->$method();
    }

    /**
     * Renders Grid column for list value
     * @param ActiveRecord|bool $model
     * @param $attribute
     * @param array $options
     * @return array
     */
    public static function linkFormat($model, $attribute, $options = [])
    {
        return array_merge([
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => !$model->isNewRecord
                ? Html::a($model->$attribute, ['view', 'id' => $model->primaryKey])
                : function ($data) use ($attribute) {
                    return Html::a($data->$attribute, ['view', 'id' => $data->primaryKey]);
                },
            'visible' => $model->isAttributeSafe($attribute),
        ], $options);
    }

    /**
     * Renders grid column for list value of via table data
     * @param ActiveRecord|bool $model
     * @param $attribute
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public static function viaListFormat($model, $attribute, $options = [])
    {
        $relation = $model->getRelation($attribute);
        $relationClass = $relation->modelClass; /** @var ActiveRecord $relationClass */
        $columns = Yii::$app->db->getTableSchema($relationClass::tableName())->columnNames;
        $titles = array_intersect(['title', 'name', 'username'], $columns);
        if (!$title = reset($titles)) {
            throw new \Exception(Yii::t('app', 'Relation does not have any title column'));
        }
        return array_merge([
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => !$model->isNewRecord
                ? implode(', ', $relation->select($title)->column())
                : function ($data) use ($attribute, $title) {
                    return implode(', ', $data->getRelation($attribute)->select($title)->column());
                },
            'filter' => false,
            //'visible' => $model->isAttributeSafe($attribute),
        ], $options);
    }

    /**
     * Renders Date format Grid column
     * @param $model
     * @param $attribute
     * @param array $options
     * @param array $pickerOptions
     * @return array
     * @throws \Exception
     */
    public static function dateFormat($model, $attribute, $options = [], $pickerOptions = [])
    {
        $pickerOptions = array_merge([
            'language' => Yii::$app->language == 'en' ? 'en-US' : Yii::$app->language,
            'model' => $model,
            'attribute' => $attribute,
            'options' => ['class' => 'form-control'],
        ], $pickerOptions);
        return array_merge([
            'attribute' => $attribute,
            'format' => 'date',
            'filter' => DatePicker::widget($pickerOptions)
        ], $options);
    }

    /**
     * Renders Date format Grid column
     * @param $model
     * @param $attribute
     * @param array $options
     * @return array
     * @throws \Throwable
     */
    public static function dateRangeFormat($model, $attribute, $options = [])
    {
        return array_merge([
            'attribute' => $attribute,
            'format' => 'date',
            'filter' => DateRangePicker::widget([
                'language' => Yii::$app->language == 'en' ? 'en-US' : Yii::$app->language,
                'model' => $model,
                'attribute' => $attribute,
                'pluginOptions' => ['locale' => ['format' => 'YYYY-MM-DD']],
                'options' => ['class' => 'form-control', 'autocomplete' => 'off'],
            ])
        ], $options);
    }

    /**
     * Renders Grid column for array value
     * @param ActiveRecord|bool $model
     * @param $attribute
     * @param array $options
     * @return array
     */
    public static function arrayFormat($model, $attribute, $options = [])
    {
        $listMethod = static::listName($attribute);
        $replacer =  method_exists($model, $listMethod)
            ? function ($v) use ($model, $listMethod) {
                return implode(', ', array_intersect_key(call_user_func([$model,$listMethod]), array_flip($v)));
            }
            : function($v){
                return '<pre>'.preg_replace(['#[\s\n]*\)#', '#Array[\s\n]*\(#'], ['', ''], print_r($v, true)).'</pre>';
            };
        return array_merge([
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => !$model->isNewRecord
                ? $replacer($model->$attribute)
                : function ($data) use ($attribute, $replacer) {
                    return $replacer($data->$attribute);
                },
            'visible' => $model->isAttributeSafe($attribute),
        ], $options);
    }

    public static function currencyFormat($attribute, $options = [])
    {
        return array_merge([
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => function (ActiveRecord $v) use ($attribute) {
                return Yii::$app->formatter->asCurrency($v->$attribute, $v->currency);
            },
        ], $options);
    }

    /**
     * Creates array of replacements for {{modelName_attribute}} placeholders.
     * @param Model[] $models
     * @param array $attributes
     * @param bool $preview
     * @return array
     *
     * @example this will add '{{myModel_title}} | title' string to the detail view
        \yii\widgets\DetailView::widget([
            'model' => false,
            'attributes' => GridHelper::variableReplacements([], ['\MyModel' => ['title']], true),
        ])
     * @example This will make an array ["{{myModel_title}}" => "Hello"]
     * where Hello is the title of \MyModel instance from $models
     * $replacements = GridHelper::variableReplacements($models, ['\MyModel' => ['title']], true)
     */
    public static function variableReplacements(array $models, array $attributes, $preview = false)
    {
        $result = [];
        if (!$models) {
            array_walk($attributes, function ($v, $class) use (&$models) {
                $models[] = new $class();
            });
        }
        foreach ($models as $model) {
            if (!is_object($model)) {
                continue;
            }
            $class = get_class($model);
            foreach ($attributes[$class] as $attributeData) {
                @list($attribute, $format) = explode(':', $attributeData);
                $formName = str_replace(['app\modules', 'models', '\\'], ['','_',''], $class);
                $key = strtolower('{{'.$formName.'_'.$attribute.'}}');
                if ($preview) {
                    $result[] = ['label' => $key, 'value' => $model->getAttributeLabel($attribute)];
                } else {
                    $result[$key] = $format
                        ? Yii::$app->formatter->format($model->$attribute, $format)
                        : $model->$attribute;
                }
            }
        }
        return $result;
    }



}