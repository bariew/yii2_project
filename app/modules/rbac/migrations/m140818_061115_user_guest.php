<?php

use yii\db\Schema;
use yii\db\Migration;
use app\modules\rbac\models\AuthItem;
use app\modules\rbac\models\AuthItemChild;

class m140818_061115_user_guest extends Migration
{
    public function getRoles()
    {
        return [
            AuthItem::ROLE_GUEST
        ];
    }

    public function up()
    {
        foreach ($this->getRoles() as $role) {
            $this->insert(AuthItem::tableName(), [
                'name'  => $role,
                'type'  => \yii\rbac\Item::TYPE_ROLE
            ]);
        }
        return $this->insert(AuthItemChild::tableName(), [
            'parent'  => AuthItem::ROLE_ROOT,
            'child'   => AuthItem::ROLE_GUEST
        ]);
    }

    public function down()
    {
        AuthItemChild::deleteAll(['parent' => $this->getRoles()]);
        AuthItemChild::deleteAll(['child' => $this->getRoles()]);
        AuthItem::deleteAll(['name' => $this->getRoles()]);
        return true;
    }
}
