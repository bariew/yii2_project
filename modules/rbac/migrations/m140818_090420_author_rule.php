<?php

use yii\db\Schema;
use yii\db\Migration;
use app\modules\rbac\models\rules\AuthorRule;
use app\modules\rbac\models\AuthRule;

class m140818_090420_author_rule extends Migration
{
    public function safeUp()
    {
        $rule = new AuthorRule([
            'name' => 'author_rule',
            'createdAt' => time(),
        ]);
        $model = new AuthRule([
            'name' => 'author_rule',
            'created_at' => time(),
            'rule'  => $rule
        ]);
        return $model->save(false);
    }

    public function down()
    {
        return AuthRule::deleteAll(['name' => 'author_rule']);
    }
}
