<?php

use yii\db\Migration;
use app\modules\product\models\Category;

class m150327_064902_product_category_root extends Migration
{
    private $item = [
        'name' => 'Categories',
        'title' => 'Categories',
        'is_active' => 0
    ];

    public function up()
    {
        $root = new Category($this->item);
        $root->makeRoot();
        return true;
    }

    public function down()
    {
        $this->delete(Category::tableName(), $this->item);
        return true;
    }
}
