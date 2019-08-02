<?php

use \app\modules\rbac\models\AuthItem;
use \app\modules\rbac\models\AuthItemChild;
use yii\db\Migration;

class m140723_145533_rbac_roles_add extends Migration
{
    public function getRoles()
    {
        return [AuthItem::ROLE_ROOT, AuthItem::ROLE_DEFAULT];
    }

    public function safeUp()
    {
        foreach ($this->getRoles() as $role) {
            $this->insert(AuthItem::tableName(), [
                'name'  => $role,
                'type'  => \yii\rbac\Item::TYPE_ROLE
            ]);
        }
        $this->insert(AuthItemChild::tableName(), [
            'parent'  => AuthItem::ROLE_ROOT,
            'child'   => AuthItem::ROLE_DEFAULT
        ]);
        return true;
    }

    public function safeDown()
    {
        AuthItemChild::deleteAll(['parent' => $this->getRoles()]);
        AuthItemChild::deleteAll(['child' => $this->getRoles()]);
        AuthItem::deleteAll(['name' => $this->getRoles()]);
        return true;
    }
}
