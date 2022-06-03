<?php

use yii\db\Schema;
use yii\db\Migration;
use app\modules\user\models\Auth;

class m150407_062517_user_auth extends Migration
{
    public function up()
    {
        $this->createTable('{{%user_auth}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'name' => $this->smallInteger(3),
            'service_id' => $this->string(),
            'created_at' => $this->integer(),
            'data' => $this->text()
        ]);
        \app\modules\common\helpers\DbHelper::addForeignKey('{{%user_auth}}', 'user_id', '{{%user}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%user_auth}}');
    }
}
