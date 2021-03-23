<?php

use yii\db\Migration;

/**
 * Class m210323_104223_create_nalog_income
 */
class m210323_104223_create_nalog_transaction extends Migration
{
    private $table = '{{%nalog_transaction}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%nalog_source}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'name' => $this->string(),
            'currency' => $this->string(3),
            'description' => $this->text(),
        ]);
        \app\modules\common\helpers\DbHelper::addForeignKey('{{%nalog_source}}', 'user_id', '{{%user}}', 'id');
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger()->defaultValue(0),
            'user_id' => $this->integer(),
            'source_id' => $this->integer(),
            'date' => $this->dateTime(),
            'amount' => $this->decimal(20, 2),
            'currency' => $this->string(3),
            'description' => $this->text(),
        ]);
        \app\modules\common\helpers\DbHelper::addForeignKey($this->table, 'user_id', '{{%user}}', 'id');
        \app\modules\common\helpers\DbHelper::addForeignKey($this->table, 'source_id', '{{%nalog_source}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->table);
        $this->dropTable('{{%nalog_source}}');
    }

}
