<?php
/**
 * ViewAccess class file.
 */

namespace app\modules\rbac\components;

use \Yii;
use yii\base\Object;
use yii\base\ViewEvent;
use app\modules\rbac\helpers\UrlHelper;
use app\modules\rbac\models\AuthItem;
use yii\console\Application;

/**
 * Manages View access: removes restricted elements.
 *
 * Usage: attach this class via events (see https://packagist.org/packages/bariew/yii2-event-component)
 *   'yii\web\View' => [
 *       'afterRender' => [
 *           ['app\modules\rbac\components\ViewAccess', 'afterRender'],
 *       ],
 *   ],
 *
 */
class ViewAccess extends Object
{
    /**
     * Checks whether links are available and removes/disables them.
     * @param string $content view event.
     */
    public static function denyLinks($content)
    {
        $doc = \phpQuery::newDocumentHTML($content);
        foreach ($doc->find('a') as $el) {
            $link = pq($el);
            if (static::checkHrefAccess($link->attr('href'))) {
                continue;
            }
            $link->remove();
        }

        foreach ($doc->find('ul.dropdown-menu') as $el) {
            $ul = pq($el);
            if (!$ul->find('a[href!="#"]')->length) { 
                $ul->parent('li.dropdown')->addClass('hide');
            }
        }
        return $doc;
    }

    /**
     * Checks link access with rbac AuthItem.
     * @param string $href url.
     * @return boolean whether link is accessable.
     */
    public static function checkHrefAccess($href)
    {
        if (!$rule = UrlHelper::rule($href)) {
            return true;
        }
        return AuthItem::checkAccess($rule);
    }
}
