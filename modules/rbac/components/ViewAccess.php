<?php
/**
 * ViewAccess class file.
 */

namespace app\modules\rbac\components;

use app\modules\rbac\helpers\UrlHelper;
use app\modules\rbac\models\AuthItem;

/**
 * Manages View access: removes restricted elements.
 *
 * Usage: attach this class via events
 *   'yii\web\View' => [
 *       'afterRender' => [
 *           ['app\modules\rbac\components\ViewAccess', 'afterRender'],
 *       ],
 *   ],
 *
 */
class ViewAccess
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
