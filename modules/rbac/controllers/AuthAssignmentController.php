<?php
/**
 * AuthAssignmentController class file.
 */
namespace app\modules\rbac\controllers;
use yii\web\Controller;
use \app\modules\rbac\models\AuthItem;
use Yii;
use \app\modules\rbac\models\AuthAssignment;
use yii\helpers\Html;
use yii\web\HttpException;
/**
 * Контроллер отвечает за назначение определенных "ролей" для "пользователей".
 *
 * @see yii\rbac\Role
 * @see yii\rbac\Permission
 * @see yii\rbac\DbManager
 */
class AuthAssignmentController extends Controller
{
    public $layout = '@app/modules/admin/views/layouts/main';

    /**
     * @inheritdoc
     */
    protected $modelClass = 'AuthAssignment';
    /**
     * Название раздела.
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('modules/rbac', 'title_roles_to_managers');
    }
    public function actionRoleUsers($name)
    {
        $users = AuthAssignment::userList();
        $role = AuthItem::findOne($name);
        if (Yii::$app->request->isPost) {
            AuthAssignment::deleteAll(['item_name' => $name]);
            foreach (Yii::$app->request->post('ids') as $user_id) {
                Yii::$app->authManager->assign($role, $user_id);
            }
        }
        echo $this->renderAjax('role-users', compact('role', 'users'));
    }

}