<?php
/**
 * UserItemController class file.
 */

namespace app\modules\post\controllers;
use app\modules\post\actions\IndexAction;
use app\modules\post\actions\ViewAction;
use app\modules\post\models\Item;

/**
 * Description.
 *
 * Usage:
 *
 */
class DefaultController extends ItemController
{
    /**
     * Gets scenario for model.
     * @return string
     */
    public function getScenario()
    {
        return Item::SCENARIO_DEFAULT;
    }

    public function actions()
    {
        return [
            'index' => IndexAction::className(),
            'view'  => ViewAction::className(),
        ];
    }
}
