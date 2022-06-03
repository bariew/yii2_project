<?php
/**
 * UserItemController class file.
 */

namespace app\modules\product\controllers;
use app\modules\product\models\Item;

/**
 * Description.
 *
 * Usage:
 *
 */
class UserItemController extends ItemController
{
    /**
     * Gets scenario for model.
     * @return string
     */
    public function getScenario()
    {
        return Item::SCENARIO_USER;
    }
}
