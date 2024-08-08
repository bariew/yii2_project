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
            'name' => $this->string(3),
            'service_id' => $this->string(),
            'created_at' => $this->integer(),
            'data' => $this->text()
        ]);
        \app\modules\common\helpers\DbHelper::addForeignKey('{{%user_auth}}', 'user_id', '{{%user}}', 'id');
        (new \app\modules\rbac\models\AuthAssignment(['item_name' => \app\modules\rbac\models\AuthItem::ROLE_ROOT, 'user_id' => 1]))->save();

    }

    public function down()
    {
        $this->dropTable('{{%user_auth}}');
    }
}
