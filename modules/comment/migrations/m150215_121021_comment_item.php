<?php

use yii\db\Schema;
use yii\db\Migration;
use app\modules\comment\models\Comment;
class m150215_121021_comment_item extends Migration
{
    public function up()
    {
         $this->createTable('{{%comment}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER,
            'parent_class' => Schema::TYPE_STRING,
            'parent_id' => Schema::TYPE_INTEGER,
            'branch_id' => Schema::TYPE_INTEGER,
            'content' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'active' => Schema::TYPE_BOOLEAN,
        ]);
    }

    public function down()
    {
         $this->dropTable('{{%comment}}');
    }
}
