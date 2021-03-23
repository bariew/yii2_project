<?php
/**
 * ListValidator class file.
 */

namespace app\modules\common\components\validators;

use yii\helpers\Inflector;
use yii\validators\Validator;

/**
 * Runs model attribute validation searching the value in related attribute value => name list
 * E.g. if you are validating status or status_id attribute you should have statusList() method
 * with all available statuses as keys of the returned array.
 *
 */
class ListValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.'); //common range validator message
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if ($this->skipOnEmpty && $model->$attribute === '') {
            return;
        }
        $method = Inflector::camelize(str_replace(['_ids', '_id'], ['', ''], $attribute).'List');
        $list = array_map(function($v){ return (string) $v; }, array_keys($model->$method()));
        $result = is_array($model->$attribute)
            ? !array_diff($model->$attribute, $list)
            : in_array($model->$attribute, $list);
        if (!$result) {
            $this->addError($model, $attribute, $this->message);
        }
    }
}
