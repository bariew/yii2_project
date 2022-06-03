<?php
/**
 * RelationViaBehavior class file.
 */

namespace app\modules\common\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * Description.
 *
 * @example
 * Add this class to owner behaviors() method:
 *
[
'class' => RelationViaBehavior::className(),
'attribute' => 'users'
]
 *
 * @property ActiveRecord $owner
 * @author Pavel Bariev <bariew@yandex.ru>
 */
class RelationViaBehavior extends Behavior
{
    /** @var string relation name you want to process by behavior */
    public $attribute;
    /** @var callable function($added:int[], $deleted:int[]) { user defined method to call after relation has been updated }*/
    public $callback;
    /** @var array */
    private $post;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    /**
     * Adds/removes relations sent with POST from via table
     */
    public function afterSave()
    {
        if ($this->post === null) {
            return;
        }
        $relation = $this->owner->getRelation($this->attribute);
        /** @var ActiveQuery $via */
        $via = is_array($relation->via) ? $relation->via[1] : $relation->via;
        /** @var \yii\db\ActiveRecord $viaClass */
        $viaTable = explode(' ', reset($via->from))[0];
        $link = $relation->link;
        $condition = [];
        foreach ($via->link as $viaAttribute => $ownerAttribute) {
            $condition[$viaAttribute] = $this->owner->$ownerAttribute;
        }
        $newIds = array_unique(array_filter($this->post, function($v){return !empty($v);}));
        $oldIds = $relation->select(array_keys($link))->column();
        \Yii::$app->db->createCommand()->delete($viaTable,
            array_merge($condition, [reset($link) => array_diff($oldIds, $newIds)])
        )->execute();
        \Yii::$app->db->createCommand()->batchInsert($viaTable, array_keys($condition)+$link, array_map(function ($id) use ($condition) {
            return array_merge($condition, [$id]);
        }, array_diff($newIds, $oldIds)))->execute();
        if ($this->callback) {
            call_user_func($this->callback, array_diff($newIds, $oldIds), array_diff($oldIds, $newIds));
        }
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $name == $this->attribute || parent::canSetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if ($name != $this->attribute) {
            return parent::__set($name, $value);
        }
        $value =  array_filter((array) $value);
        if ($this->post === $value) {
            return ;
        }
        $this->post = $value;
        $relation = $this->owner->getRelation($this->attribute);
        /** @var ActiveRecord $class */
        $class = $relation->modelClass;
        $this->owner->populateRelation(
            $this->attribute,
            $class::find()
                ->from($relation->from)
                ->where($relation->where)
                ->andWhere(['id' => $this->post])
                ->onCondition($relation->on)
                ->indexBy($relation->indexBy)
                ->orderBy($relation->orderBy)
                ->all()
        );
    }
}
