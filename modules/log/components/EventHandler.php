<?php
/**
 * EventHandler class file.
 */

namespace app\modules\log\components;

use app\modules\log\models\Item;
use yii\base\Event;

/**
 * Description.
 *
 * Usage:
 *
 */
class EventHandler
{
    /**
     * @param Event $event
     * @param array $attributes
     * @return bool
     */
    public static function common(Event $event, $attributes = [])
    {
        return Item::create($event, $attributes)->save(false);
    }
}