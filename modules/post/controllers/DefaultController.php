<?php
/**
 * UserItemController class file.
 */

namespace app\modules\post\controllers;

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

}
