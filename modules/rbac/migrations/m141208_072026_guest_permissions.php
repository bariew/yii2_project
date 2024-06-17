<?php

use yii\db\Schema;
use yii\db\Migration;
use \app\modules\rbac\models\AuthItem;
use app\modules\rbac\models\AuthItemChild;

class m141208_072026_guest_permissions extends Migration
{
    private function getPermissions()
    {
        $guestAccess = [
            'common/site',
            'lesson/dashboard',
            'page/default',
            'user/default',
            'debug/default/toolbar',
        ];
        return [
            AuthItem::ROLE_GUEST => $guestAccess,
            AuthItem::ROLE_DEFAULT => $guestAccess,
        ];
    }

    public function up()
    {
        foreach ($this->getPermissions() as $role => $permissions) {
            foreach ($permissions as $permission) {
                try {
                    $this->insert(AuthItem::tableName(), [
                        'name'  => $permission,
                        'type'  => \yii\rbac\Item::TYPE_PERMISSION
                    ]);
                } catch (Exception $e) {}
                try {
                    $this->insert(AuthItemChild::tableName(), [
                        'parent'  => $role,
                        'child'   => $permission
                    ]);
                } catch (Exception $e) {}
            }
        }
        return true;
    }

    public function down()
    {
        foreach ($this->getPermissions() as $role => $permissions) {
            AuthItemChild::deleteAll(['parent' => $role, 'child' => $permissions]);
            AuthItem::deleteAll(['name' => $permissions]);
        }

        return true;
    }
}
