<?php
/**
 * UrlHelper class file.
 */
namespace app\modules\rbac\helpers;

use yii\web\Request;
use yii\helpers\Url;
use \Yii;

/**
 * Helper for urls operations.
 *
 */
class UrlHelper extends Url
{
    /**
     * @var Request request keeper.
     */
    protected static $_request;

    /**
     * Gets router rule for url.
     * @param string $url url
     * @return mixed boolean or array [module, controller, action] ids.
     */
    public static function rule($url)
    {
        $baseUrl = Yii::$app->request->hostInfo;
        $parsedUrl = parse_url($url);
        if (isset($parsedUrl['host']) && !strpos($baseUrl, $parsedUrl['host'])) {
            return false;
        }
        if (!isset($parsedUrl['path'])) {
            return false;
        }
        $path = str_replace('/' . basename(Yii::$app->request->scriptFile), '', $parsedUrl['path']);
        $request = static::$_request ? static::$_request : (static::$_request = new Request());
        $request->setPathInfo($path);
        $rule = explode('/', Yii::$app->urlManager->parseRequest($request)[0]);

        if (count($rule) == 2) {
            array_unshift($rule, Yii::$app->id);
        }
        if (count($rule)!=3) {
            return false;
        }
        return array_combine(['module', 'controller', 'action'], $rule);
    }
}