<?php
/**
 * Created by PhpStorm.
 * User: pt
 * Date: 8/18/14
 * Time: 12:17 PM
 */

namespace app\modules\rbac\components;


use app\modules\rbac\models\AuthAssignment;
use app\modules\rbac\models\AuthItem;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\base\ViewEvent;
use Yii;
use yii\console\Application;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

class EventHandlers
{
    /**
     * Runs access methods for view event.
     * @param ViewEvent $event view event.
     * @return bool
     */
    public static function afterViewRenderLinkRemove($event)
    {
        if (get_class(Yii::$app) == Application::className()) {
            return true;
        }
        if (in_array(Yii::$app->controller->module->id, ['gii', 'debug'])) {
            return true;
        }
        $event->output = ViewAccess::denyLinks($event->output);
    }
    
    public static function responseAfterPrepare($event)
    {
        if (get_class(Yii::$app) == Application::className()) {
            return true;
        }
        if (!in_array(Yii::$app->controller->module->id, AuthItem::ACCESS_MODULES)) {
            return true;
        }

        $event->sender->content = ViewAccess::denyLinks($event->sender->content);
    }


    /**
     * Checks whether current user has access to current controller action.
     * @param Event $event controller beforeAction event.
     * @throws \yii\web\HttpException
     */
    public static function beforeActionAccess(ActionEvent $event)
    {
        /** @var Controller $controller */
        $controller = $event->sender;
        if (!$controller->module || in_array($controller->module->id, AuthItem::ACCESS_MODULES)) {
            return;
        }
        $permissionName = AuthItem::createPermissionName([$controller->module->id, $controller->id, $controller->action->id]);
        if (!AuthItem::checkAccess($permissionName, Yii::$app->user)) {
            throw new ForbiddenHttpException("You have no access rights");
        }
    }

    public static function afterActionModelAccess($event)
    {
        /**
         * @var Controller $controller
         */
        $model = $event->sender;
        $controller = Yii::$app->controller;
        $permissionName = AuthItem::createPermissionName([$controller->module->id, $controller->id, $controller->action->id]);

        if (!AuthItem::checkAccess($permissionName, false, compact('model'))) {
            throw new HttpException(403, Yii::t('modules/rbac', 'Access denied'));
        }
    }

    public static function userDefaultRoleAssignment($event)
    {
        return ($default = AuthItem::findOne(AuthItem::ROLE_DEFAULT))
            ? Yii::$app->authManager->assign($default, $event->sender->primaryKey)
            : false;
    }

    public static function userRolesRemove($event)
    {
        return AuthAssignment::deleteAll(['user_id' => $event->sender->primaryKey]);
    }
} 