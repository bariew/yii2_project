<?php
/**
 * DbHelper class file.
 */

namespace app\modules\common\helpers;

use Yii;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;
use yii\db\Connection;
use yii\db\Query;

/**
 * Description:
 */
class DbHelper
{
    /**
     * Inserts OR updates data if primary key exists
     * @param $table
     * @param $data
     * @param Connection $db
     * @return int|void
     * @throws \yii\db\Exception
     */
    public static function insertUpdate($table, $data, $db = null)
    {
        if(!$data || !is_array($data) || !($firstRow = reset($data))){
            return;
        }
        if(!$fields = array_keys($firstRow)){
            return;
        };
        $db = $db ?? Yii::$app->db;
        return $db->createCommand($db->queryBuilder->batchInsert($table, $fields, $data) . ' ON DUPLICATE KEY UPDATE ' .
            join(', ', array_map(function($field) { return $field . ' = VALUES(' . $field . ')'; }, $fields))
        )->execute();
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
