<?php
/**
 * DbHelper class file.
 */

namespace app\modules\common\helpers;

use Yii;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;
use yii\db\Query;

/**
 * Description:
 */
class DbHelper
{
    /**
     * @param $class ActiveRecord|string
     * @param $data array
     */
    public static function batchInsert($class, $data)
    {
        if (!$data) {
            return;
        }
        $columns = [];
        foreach ($data as $key => $datum) {
            $columns = array_keys($datum);
            $data[$key] = array_values($datum);
        }
        \Yii::$app->db->createCommand()->batchInsert($class::tableName(), $columns, $data)->execute();
        foreach ($data as $datum) {
            $model = Yii::createObject($class, array_combine($columns, $datum));
            AfterSaveEvent::trigger($model, $class::EVENT_AFTER_INSERT);
        }
    }

    /**
     * Inserts new data into table or updates on duplicate key.
     *
     * $data keys are ignored as per Yii batch_insert
     *
     * @param string $tableName db table name
     * @param array $data data to insert
     * @param array $columns db column names
     * @param string $db application connection name
     * @return boolean whether operation is successful
     */
    public static function insertUpdate($tableName, $data, $columns = null, $db = 'db')
    {
        if (!$data) {
            return false;
        }
        foreach ($data as $key => $row) {
            $columns = $columns ? : array_keys($row);
            $data[$key] = array_values($row);
        }
        $sql = \Yii::$app->$db->createCommand()->batchInsert($tableName, $columns, array_values($data))->getSql()
            . ' ON DUPLICATE KEY UPDATE '
            . implode(', ', array_map(function ($v) { return "`{$v}` = VALUES(`{$v}`)"; }, $columns));
        return \Yii::$app->$db->createCommand($sql)->execute();
    }

    /**
     * Default ForeignKey name
     * @param $table
     * @param $column
     * @return string
     */
    public static function fkName($table, $column)
    {
        return 'fk_'.str_replace(['{{%', '}}'], ['', ''], $table) . '_' . implode('_', (array) $column);
    }

    /**
     * Default ForeignKey name
     * @param $table
     * @param $column
     * @return string
     */
    public static function indexName($table, $column)
    {
        return substr('idx_'.str_replace(['{{%', '}}'], ['', ''], $table) . '_' . implode('_', array_map(function ($column) {
            return explode('(', $column)[0];// sometimes $column is named like column_name(25) // chars length
        }, (array) $column)), 0, 64);
    }

    /**
     * @param $table
     * @param $column
     * @param $fTable
     * @param $fColumn
     * @param string $delete
     * @param string $update
     * @return int
     */
    public static function addForeignKey($table, $column, $fTable, $fColumn, $delete = 'CASCADE', $update = 'CASCADE')
    {
        return Yii::$app->db->createCommand()->addForeignKey(
            static::fkName($table, $column), $table, $column, $fTable, $fColumn, $delete, $update
        )->execute();
    }

    /**
     * @param $table
     * @param $column
     * @return int
     */
    public static function dropForeignKey($table, $column)
    {
        return Yii::$app->db->createCommand()->dropForeignKey(static::fkName($table, $column), $table)->execute();
    }

    /**
     * @param $table
     * @param $column
     * @param bool $unique
     * @return int
     */
    public static function createIndex($table, $column, $unique = false)
    {
        return Yii::$app->db->createCommand()->createIndex(static::indexName($table, $column), $table, $column, $unique)->execute();
    }

    /**
     * @param $table
     * @param $column
     * @return int
     */
    public static function dropIndex($table, $column)
    {
        return Yii::$app->db->createCommand()->dropIndex(static::indexName($table, $column), $table)->execute();
    }
}
