<?php
/**
 * ActiveQueryCached class file
 */

namespace app\modules\common\components;

use Yii;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Trait ActiveRecordCachedTrait
 * @package app\modules\common\components
 *
 * @mixin ActiveRecord
 */
trait ActiveRecordCachedTrait
{
    /**
     * @inheritDoc
     */
    public static function find()
    {
        $result = \Yii::createObject(ActiveQuery::className(), [static::class]); /** @var ActiveQuery $result */
        return $result->cache(true, new TagDependency(['tags' => static::tableName()]));
    }

    /**
     * @inheritDoc
     */
    public static function updateAll($attributes, $condition = '', $params = [])
    {
        if ($result = parent::updateAll($attributes, $condition, $params)) {
            TagDependency::invalidate(Yii::$app->cache, static::tableName());
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public static function updateAllCounters($counters, $condition = '', $params = [])
    {
        if ($result = parent::updateAllCounters($counters, $condition, $params)) {
            TagDependency::invalidate(Yii::$app->cache, static::tableName());
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($result = parent::save($runValidation, $attributeNames)) {
            TagDependency::invalidate(Yii::$app->cache, static::tableName());
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function delete()
    {
        if ($result = parent::delete()) {
            TagDependency::invalidate(Yii::$app->cache, static::tableName());
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public static function deleteAll($condition = null, $params = [])
    {
        if ($result = parent::deleteAll($condition, $params)) {
            TagDependency::invalidate(Yii::$app->cache, static::tableName());
        }
        return $result;
    }

    /**
     * @param $columns
     * @param $data
     * @return int
     * @throws \yii\db\Exception
     */
    public static function insertAll($columns, $data)
    {
        if (!$data) {
            return 0;
        }
        TagDependency::invalidate(Yii::$app->cache, static::tableName());
        static::getDb()->createCommand()->batchInsert(static::tableName(), $columns, $data)->execute();
    }
}