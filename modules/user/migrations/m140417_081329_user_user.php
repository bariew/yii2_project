<?php
use app\modules\user\models\User;

class m140417_081329_user_user extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', array(
            'id'            => 'pk',
            'email'         => 'string',
            'password'      => 'string',
            'auth_key'      => 'string',
            'api_key'       => 'string',
            'username'      => 'string',
            'company_name'  => 'string',
            'status'        => 'integer',
            'created_at'    => 'integer',
            'updated_at'    => 'integer',
        ));

        $this->insert(User::tableName(), array(
            'id'            => 1,
            'email'         => 'user1@user1.user1',
            'password'      => '$2y$13$zxH1XXv656hqeaPPzo7yyeSvaaVx5nAtfU7xraAOB9.E720DwJe2e',//123123
            'username'      => 'admin',
            'company_name'  => 'admin',
            'status'        => 10,
            'created_at'    => time(),
        ));
        (new \app\modules\rbac\models\AuthAssignment(['item_name' => \app\modules\rbac\models\AuthItem::ROLE_ROOT, 'user_id' => 1]))->save();
    }

    public function down()
    {
         $this->dropTable('{{%user}}');
    }
}
