<?php
/**
 * RelationBehavior class file.
 */


namespace app\modules\common\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * Uses callbacks to process ActiveRecord relation saving events.
 *
 * @property ActiveRecord $owner
 * @example
 * in model rules:
 * ```
 * [
 * ...
 *      ['myRelation', 'safe']
 * ]
 * ```
 *
 * in view:
 * ```
 * $form->field($model, 'myRelation')->checkboxList($availableRelationValues, [
 *      'value' => $model->getMyRelation()->select('name')->indexBy('id')->column()
 * ])
 * ```
 *
 * in behaviors:
 * ```
...
[
'class' => RelationBehavior::class,
'relation' => 'myRelation',
'events' => [
ActiveRecord::EVENT_BEFORE_VALIDATE => function($values) {
if (in_array(1, $values)) {
$this->addError('myRelation', 'Forbidden value 1');
}
},
RelationBehavior::EVENT_AFTER_SAVE => function ($values) {
MyRelationClass::deleteAll(['user_id' => $this->id]);
foreach($values as $value) {
$this->link('myRelation', new MyRelationClass(['user_id' => $this->id, 'name' => $value]));
}
}
],
]
```
 */
class RelationBehavior extends Behavior
{
    const EVENT_AFTER_LOAD = 'afterLoad';
    const EVENT_AFTER_VALIDATE = 'afterValidate';
    const EVENT_AFTER_SAVE = 'afterSave';

    const METHOD_DEFAULT_LOAD = 'defaultLoad';
    const METHOD_DEFAULT_VALIDATE = 'defaultValidate';
    const METHOD_DEFAULT_SAVE = 'defaultSave';
    const METHOD_DEFAULT_DELETE = 'defaultDelete';

    /**
     * @var string relation name
     */
    public $attribute;
    public $cloneCallback; //process this->cloneIds if models have been cloned: change their "parent_id" etc in the callback

    /**
     * @var array
     */
    private $data;
    private $cloneIds = [];

    /**
     * Event callbacks
     * @var array
     */
    public $events = [
        self::EVENT_AFTER_LOAD => [self::class, self::METHOD_DEFAULT_LOAD],
        ActiveRecord::EVENT_AFTER_VALIDATE => [self::class, self::METHOD_DEFAULT_VALIDATE],
        self::EVENT_AFTER_SAVE => [self::class, self::METHOD_DEFAULT_SAVE],
        ActiveRecord::EVENT_BEFORE_DELETE => [self::class, self::METHOD_DEFAULT_DELETE],
    ];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => function () { $this->callBack(ActiveRecord::EVENT_BEFORE_VALIDATE);},
            ActiveRecord::EVENT_AFTER_VALIDATE => function () { $this->callBack(ActiveRecord::EVENT_AFTER_VALIDATE);},
            ActiveRecord::EVENT_AFTER_INSERT => function () { $this->callBack(static::EVENT_AFTER_SAVE, ['insert' => true]);},
            ActiveRecord::EVENT_AFTER_UPDATE => function () { $this->callBack(static::EVENT_AFTER_SAVE, ['insert' => false]);},
            ActiveRecord::EVENT_BEFORE_DELETE => function () { $this->callBack(ActiveRecord::EVENT_BEFORE_DELETE);},
        ];
    }

    /**
     * Calls all relations callback that are set in the behavior settings.
     * @param string $event
     * @param array $options
     */
    private function callBack($event, $options = [])
    {
        foreach ($this->events as $name => $callback) {
            if ($name != $event || !$callback) {
                continue;
            }
            if (in_array($name, [static::EVENT_AFTER_SAVE, ActiveRecord::EVENT_BEFORE_VALIDATE, ActiveRecord::EVENT_AFTER_VALIDATE])
                && !isset($this->data)
            ) {
                // if relation data is not loaded via POST or any other 'this->relation = ...' way
                // - do not process it
                continue;
            }
            switch ($callback) {
                case static::METHOD_DEFAULT_LOAD:
                    $this->defaultLoad($this->data, $options);
                    break;
                case static::METHOD_DEFAULT_VALIDATE:
                    $this->defaultValidate($this->data, $options);
                    break;
                case static::METHOD_DEFAULT_SAVE:
                    $this->defaultSave($this->data, $options);
                    break;
                case static::METHOD_DEFAULT_DELETE:
                    $this->defaultDelete();
                    break;
                default: call_user_func_array($callback, [$this->data, $options]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        if ($this->attribute == $name) {
            return true;
        }
        return parent::canSetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $values)
    {
        if ($this->attribute != $name) {
            return parent::__set($name, $values);
        }
        $this->data = $values;
        $this->callBack(static::EVENT_AFTER_LOAD);
    }

    /**
     * Populates owner relation from POST data (or from $owner->$relation = ... setting)
     * @param $data
     * @param $options
     */
    private function defaultLoad($data, $options)
    {
        $relation = $this->owner->getRelation($this->attribute);
        if (!$data) {
            return $this->owner->populateRelation($this->attribute, $relation->multiple ? [] : $data);
        }
        $this->copyFiles();
        $this->oldChildren = $this->owner->isNewRecord
            ? ArrayHelper::index($this->owner->{$this->attribute}, 'id')
            : ArrayHelper::index($relation->all(), 'id');
        $this->populateData($data, $this->oldChildren, false);
    }

    private $oldChildren = [];

    /**
     * Validates owner relation
     * @param $data
     * @param $options
     */
    private function defaultValidate($data, $options)
    {
        foreach ($this->getRelatedItems() as $key => $item) {
            if (!$item->validate()) {
                foreach ($item->getFirstErrors() as $attribute => $firstError) {
                    $this->owner->addError($this->attribute . "[{$key}][$attribute]", $firstError);
                }
            }
        }
    }

    /**
     * @param $data
     * @param $options
     * @return int
     */
    private function defaultSave($data, $options)
    {
        if (!$data) {
            return $options['insert']
                ? true
                : static::deleteAllItems($this->oldChildren);
        }
        static::deleteAllItems(array_diff_key($this->oldChildren, $data));
        $this->populateData($data, $this->oldChildren, true);
        if ($this->cloneIds && $this->cloneCallback) {
            call_user_func($this->cloneCallback, $this->cloneIds);
        }
    }

    /**
     * @param $data
     * @param $oldChildren
     * @param $save
     */
    private function populateData($data, $oldChildren, $save)
    {
        $relation = $this->owner->getRelation($this->attribute);
        $children = [];
        foreach ((array) $data as $key => $row) {
            if ($key === 'TEMPLATE') {
                continue;
            }
            if ($child = $this->createRelationModel($this->owner, $this->attribute, $row, @$oldChildren[($row['id'] ?? $key)], $save)) {
                $child->setAttributes(['id' => $child->primaryKey ? : $key], false);
                $children[$child->primaryKey] = $child;
            }
        }
        $this->owner->populateRelation($this->attribute, ($relation->multiple ? $children : reset($children)));
    }

    /**
     *
     */
    private function defaultDelete()
    {
        foreach ((array) $this->owner->{$this->attribute} as $item) {
            if (!$item instanceof ActiveRecord) {
                continue;
            }
            $item->delete();
        }
    }

    /**
     * @return ActiveRecord[]
     */
    private function getRelatedItems()
    {
        return $this->owner->getRelation($this->attribute)->multiple
            ? $this->owner->{$this->attribute}
            : array_filter([$this->owner->{$this->attribute}]);
    }

    /**
     * Sets $_FILES data as if it was sent to relation model form
     */
    private function copyFiles()
    {
        if (!isset($_FILES[$this->owner->formName()])) {
            return;
        }
        $formName = StringHelper::basename($this->owner->getRelation($this->attribute)->modelClass);
        foreach ($_FILES[$this->owner->formName()] as $key => $values) {
            if (!isset($values[$this->attribute])) {
                return;
            }
            $_FILES[$formName][$key] = $values[$this->attribute];
        }
    }

    /**
     * @param ActiveRecord $parent
     * @param string $relationName
     * @param array $row
     * @param null|ActiveRecord $existingChild
     * @param bool $save
     * @return null|ActiveRecord
     */
    private function createRelationModel(ActiveRecord $parent, $relationName, $row, $existingChild = null, $save = true)
    {
        $relation = $parent->getRelation($relationName);
        /** @var ActiveRecord $class */
        $class = $relation->modelClass;
        $childAttributes = array_combine(
            array_keys($relation->link),
            array_intersect_key($parent->attributes, array_flip($relation->link))
        );
        /** @var ActiveRecord $child */
        $child = $existingChild ?? new $class($childAttributes);
        $child->setAttributes(array_filter($childAttributes), false); // existing child may be actually a new record cloned from an old one
        $child->load(($row instanceof ActiveRecord ? $row->attributes : $row), '');
        $child->populateRelation($relation->inverseOf, $parent);
        if ($child->isNewRecord && isset($child->id)) {
            $child->id = null;
        }
        if (!$save || !is_numeric($parent->primaryKey) || $child->save() || !$child->isNewRecord) {
            if (isset($child->clone_id)) {
                $this->cloneIds[$child->clone_id] = $child->primaryKey;
            }
            return $child;
        } else {
            return null;
        }
    }


    /**
     * @param ActiveRecord[] $models
     * @return int
     */
    private static function deleteAllItems($models)
    {
        /** @var ActiveRecord $model */
        foreach ($models as $model) {
            $model->trigger(ActiveRecord::EVENT_BEFORE_DELETE);
        }
        return $models
            ? $model::deleteAll(['id' => array_filter(\yii\helpers\ArrayHelper::getColumn($models, 'id'), 'is_integer')])
            : null;
    }
}
