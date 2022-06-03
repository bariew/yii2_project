<?php
/**
 * SortableBehavior class file.
 */


namespace app\modules\common\components\behaviors;


use common\modules\library\components\LibraryBehavior;
use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * Reorders neighbors order according to the owner order field change.
 *
 * Usage:
```
public function behaviors()
{
    return [
        [
            'class' => SortableBehavior::class,
            'attribute' => 'display_order',  // order counter e.g. 1, 2, 3, 4, etc
            'condition' => function () {}, // query to select neighbours
            'start' => 1, // default 0, where we start counting in attribute above
        ]
    ];
}
```
 * @property ActiveRecord $owner
 */
class SortableBehavior extends Behavior
{
    public $attribute = 'order'; // warning - this value is used in unbound SQL - do not allow untrusted input
    public $start = 0;
    /**
     * For searching siblings
     * @var Callable|string Callable must return a string that will be used in SQL WHERE condition, string returns an attribute for WHERE condition
     */
    public $condition;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => function () {
                // move all following records down one slot to fill gap left by deleted record.
                /** @var ActiveQuery $query */
                $query = $this->sortFindQuery()->andWhere(['>', $this->attribute, $this->owner->{$this->attribute}]);
                Yii::$app->db->createCommand()->update(
                    $this->owner->tableName(),
                    [$this->attribute => new Expression("`{$this->attribute}`-1")],
                    Yii::$app->db->queryBuilder->buildCondition($query->where, $query->params) . " ORDER BY `{$this->attribute}` ASC",
                    $query->params
                )->execute();
            },
            ActiveRecord::EVENT_BEFORE_INSERT => function () {
                if ($this->owner->isAttributeChanged($this->attribute)) {
                    // if the new order is set explicitly - shift old elements
                    /** @var ActiveQuery $query */
                    $query = $this->sortFindQuery()->andWhere(['>=', $this->attribute, $this->owner->{$this->attribute}]);
                    Yii::$app->db->createCommand()->update(
                        $this->owner->tableName(),
                        [$this->attribute => new Expression("`{$this->attribute}`+1")],
                        Yii::$app->db->queryBuilder->buildCondition($query->where, $query->params) . " ORDER BY `{$this->attribute}` DESC",
                        $query->params
                    )->execute();
                } else {
                    // If we're adding a new item, set `order` attribute to the highest number
                    // e.g. add to end of the list.
                    //
                    // if we're the first - this basically pans out to null + $this->start, so
                    // we get the starting order
                    $this->owner->setAttribute($this->attribute, $this->sortFindQuery()->count() + $this->start);
                }
            },
            ActiveRecord::EVENT_BEFORE_UPDATE => function (Event $event) {
                if (is_string($this->condition) && $this->owner->isAttributeChanged($this->condition, false)) {
                    $this->sortTransplant();
                    return;
                }
                // if we didn't change the sort order, we are all good.
                if (!$this->owner->isAttributeChanged($this->attribute)) {
                    return;
                }

                // get old and new placement
                $old = $this->owner->getOldAttribute($this->attribute);
                $new = $this->owner->getAttribute($this->attribute);

                // don't do anything if we didn't actually move
                if ($old == $new) {
                    return;
                }


                // If we have moved lower in the list (higher index)
                // we need to move everything up (lower index);
                if ($new > $old ) {
                    $move_operator = '-';
                    $direction = 'ASC';
                } else {
                    // or vise versa
                    $move_operator = '+';
                    $direction = 'DESC';
                }
                /** @var ActiveQuery $query */
                $query = $this->sortFindQuery()
                    ->andWhere(['>=', $this->attribute, min([$old, $new])])
                    ->andWhere(['<=', $this->attribute, max([$old, $new])])
                ;
                Yii::$app->db->createCommand()->update(
                    $this->owner->tableName(),
                    [$this->attribute => new Expression("`{$this->attribute}`{$move_operator}1")],
                    Yii::$app->db->queryBuilder->buildCondition($query->where, $query->params) . " ORDER BY `{$this->attribute}` {$direction}",
                    $query->params
                )->execute();
            },
        ];
    }

    /**
     * @param $n
     * @return bool
     */
    public function sort($n)
    {
        if ($n === $this->owner->{$this->attribute}) {
            return true;
        }
        // temporarily set model `order` (if attribute = order) to -1.
        // we do this so we can move around the rest of the order without this value getting in the way
        $this->owner->updateAll([$this->attribute => -1], $this->owner->getPrimaryKey(true));
        // set `order` to $n
        $this->owner->{$this->attribute} = (int) $n;
        // save
        return $this->owner->save(true, [$this->attribute]);
    }

    /**
     */
    private function sortTransplant()
    {
        $newCondition = $this->condition;
        $this->condition = function () use ($newCondition) {
            return [$newCondition => $this->owner->getOldAttribute($newCondition)];
        };
        call_user_func($this->events()[ActiveRecord::EVENT_AFTER_DELETE]);
        $this->condition = $newCondition;
        $this->owner->setAttribute($this->attribute, $this->sortFindQuery()->max("`{$this->attribute}`") + 1);
    }

    /**
     * @return ActiveQuery
     */
    private function sortFindQuery()
    {
        $condition = is_string($this->condition)
            ? [$this->condition => $this->owner->{$this->condition}]
            : call_user_func($this->condition);
        return $this->owner::find()
            ->andWhere($condition)
            ->andWhere(['!=', 'id', (int) $this->owner->primaryKey])
            ;
    }
}
