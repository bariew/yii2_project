<?php
/**
 * Created by PhpStorm.
 * User: pt
 * Date: 27.03.15
 * Time: 10:44
 */
namespace app\modules\product\components;
use creocoder\nestedsets\NestedSetsQueryBehavior;
class NestedQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}