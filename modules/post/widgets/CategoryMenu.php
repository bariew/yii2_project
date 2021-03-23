<?php
/**
 * Created by PhpStorm.
 * User: pt
 * Date: 27.03.15
 * Time: 11:28
 */

namespace app\modules\post\widgets;

use app\modules\post\Module;
use bariew\nodeTree\ARTreeMenuWidget;
use app\modules\post\models\Category;
use yii\base\Widget;

class CategoryMenu extends Widget
{
    public static $uniqueKey = 0;

    public $view = 'nested';

    public function run()
    {
        $treeWidget = new ARTreeMenuWidget([
            'items' => $this->generateItems(),
            'view' => 'nested'
        ]);
        return $treeWidget->run();
    }

    protected function generateItems()
    {
        $items = Category::find()->orderBy(['lft' => SORT_ASC])->asArray()->all();;
        foreach ($items as &$item) {
            $uniqueKey = static::$uniqueKey++;
            $nodeId = $uniqueKey . '-id-' . $item['id'];
            $item['nodeAttributes'] = [
                'id'    => $nodeId,
                'text'  => $item['name'],
                'type'  => 'folder',
                'active'=> \Yii::$app->request->get('id') == $item['id'],
                'a_attr'=> [
                    'data-id' => $nodeId,
                    'href'    => ["/post/category/update", 'id' => $item['id']]
                ]
            ];
        }
        return $items;
    }
}