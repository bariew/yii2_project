<?php

use yii\db\Migration;

class m151028_113215_log_item extends Migration
{
    private $table = '{{%log_item}}';

    public function up()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'event' => $this->string(),
            'model_name' => $this->string(),
            'model_id' => $this->string(),
            'message' => $this->text(),
            'created_at' => $this->integer(),
        ], 'ENGINE = MyISAM');
    }

    public function down()
    {
        $this->dropTable($this->table);
    }
}
