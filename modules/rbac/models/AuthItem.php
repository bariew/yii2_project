<?php
/**
 * AuthItem class file.
 */

namespace app\modules\rbac\models;

use app\controllers\SiteController;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\FileHelper;
use \yii\rbac\Item;
use yii\db\ActiveRecord;
use \app\modules\rbac\components\TreeBuilder;
use \yii\behaviors\TimestampBehavior;
use \yii\web\HttpException;

/**
 * Модель управления ролями пользователей.
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment $authAssignment
 * @property AuthRule $ruleName
 * @property AuthItemChild $authItemChild
 */
class AuthItem extends ActiveRecord
{
    const ROLE_ROOT = 'root';
    const ROLE_MANAGER = 'manager';
    const ROLE_DEFAULT = 'default';
    const ROLE_GUEST = 'guest';

    const ACCESS_MODULES = ['common'];
    /**
     * @var array container for autItem tree for menu widget.
     */
    public $childrenTree = [];

    public static $userRoles = [];
    public static $defaultRoles = [];

    public static function defaultRoleList()
    {
        return [
            static::ROLE_ROOT => Yii::t('modules/rbac', 'role_admin'),
            static::ROLE_MANAGER => Yii::t('modules/rbac', 'role_manager'),
            static::ROLE_DEFAULT => Yii::t('modules/rbac', 'role_default'),
            static::ROLE_GUEST => Yii::t('modules/rbac', 'role_guest'),
        ];
    }

    /**
     * @param $role
     * @return bool
     */
    public function isDefaultRole($role)
    {
        return in_array($role, array_keys(static::defaultRoleList()));
    }

    /**
     * @return array
     */
    public static function typeList()
    {
        return [
            Item::TYPE_ROLE => Yii::t('modules/rbac', 'role'),
            Item::TYPE_PERMISSION => Yii::t('modules/rbac', 'permission'),
        ];
    }

    /**
     * Creates valid permission name for controller action.
     * @param array $data items for permission name (moduleId, ControllerId, ActionId).
     * @return string permission name.
     */
    public static function createPermissionName($data)
    {
        return implode('/', $data);
    }

    /**
     * @param $user_id
     */
    protected static function setDefaultRoles($user_id)
    {
        static::$defaultRoles = $user_id
            ? AuthItemChild::childList(static::ROLE_DEFAULT)
            : AuthItemChild::childList(static::ROLE_GUEST);

        static::$defaultRoles
            = Yii::$app->authManager->defaultRoles
            = array_merge(
                Yii::$app->authManager->defaultRoles,
                static::$defaultRoles
            );
    }

    /**
     * Check whether the user has access to permission.
     * @param mixed $permissionName permission name or its components for static::createPermissionName.
     * @param mixed $user user
     * @param array $params
     * @return boolean whether user has access to permission name.
     */
    public static function checkAccess($permissionName, $user = false, $params = [])
    {
        if (is_array($permissionName)) {
            $permissionName = static::createPermissionName($permissionName);
        }
        if (!$user) {
            $user = Yii::$app->user;
        }

        if (!$user->isGuest && !isset(static::$userRoles[$user->id])) {
            static::$userRoles[$user->id] = Yii::$app->authManager->getRolesByUser($user->id);
        }

        if (isset(static::$userRoles[$user->id][static::ROLE_ROOT])) {
            return true;
        }

        if (!static::$defaultRoles) {
            static::setDefaultRoles($user->id);
        }

        foreach (static::$defaultRoles as $defaultRole) {
            if (strpos($permissionName, $defaultRole) === 0) {
                return true;
            }
        }

        return $user->can($permissionName, $params);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'defaultRoleRenameRule'],
            [['type'], 'default', 'value' => Item::TYPE_ROLE],
            [['created_at', 'updated_at', 'type'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['rule_name'], 'filter', 'filter' => function ($value) { return ($value) ? $value : null;}]
        ];
    }

    /**
     * @param $attribute
     */
    public function defaultRoleRenameRule($attribute)
    {
        if ($this->isAttributeChanged($attribute) && $this->isDefaultRole(@$this->oldAttributes['name'])) {
             $this->addError($attribute, Yii::t('modules/rbac', 'default_role_renaming_error'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => static::typeList()[$this->type],
            'type' => Yii::t('modules/rbac', 'Type'),
            'description' => Yii::t('modules/rbac', 'Description'),
            'rule_name' => Yii::t('modules/rbac', 'Rule name'),
            'data' => Yii::t('modules/rbac', 'Data'),
            'created_at' => Yii::t('modules/rbac', 'Created at'),
            'updated_at' => Yii::t('modules/rbac', 'Updated at'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => TreeBuilder::className(),
                'childrenAttribute' => 'childrenTree',
                'actionPath' => '/rbac/auth-item/update'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        if ($this->isDefaultRole($this->name)) {
            throw new HttpException(403, Yii::t('modules/rbac', 'default_role_delete_error'));
        }
        return Yii::$app->authManager->remove($this->getItem());
    }


    // RELATIONS

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemParents()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(static::className(), ['name' => 'parent'])->via('authItemParents');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * Gets items attached to current one by AuthItemChild relation.
     * @return ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(static::className(), ['name' => 'child'])->via('authItemChildren');
    }

    /**
     * @return ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(static::className(), ['name' => 'child'])
            ->via('authItemChildren')
            ->where(['type' => Item::TYPE_ROLE]);
    }

    /**
     * Gets permissions AuthItems attached to current model through AuthItemChild.
     * @return \yii\db\ActiveQuery search object.
     */
    public function getPermissions()
    {
        return $this->hasMany(static::className(), ['name' => 'child'])
            ->via('authItemChildren')
            ->where(['type' => Item::TYPE_PERMISSION]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * Gets items attached to current one by AuthItemChild relation.
     * @return ActiveQuery | []
     */
    public function getUsers()
    {
        if (!$user = AuthAssignment::userInstance()) {
            return [];
        }
        return $this->hasMany($user::className(), ['id' => 'user_id'])->via('authAssignments');
    }

    /**
     * Some times in views you just need give them 'id'
     * @return string model name
     */
    public function getId()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRuleName()
    {
        return $this->rule_name;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return new Item([
            'ruleName' => $this->rule_name,
            'createdAt'=> $this->created_at,
            'updatedAt'=> $this->updated_at,
            'name'      => $this->name,
            'type'      => $this->type,
            'description'=>$this->description,
            'data'      => $this->data
        ]);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function updateItem()
    {
        return Yii::$app->authManager->update($this->oldAttributes['name'], $this->getItem());
    }

    /**
     * Detaches this model from its old parent
     * and attaches to the new one.
     * @param AuthItem $oldParent item
     * @param AuthItem $newParent item
     * @return boolean whether model has been moved.
     */
    public function move($oldParent, $newParent)
    {
        return $oldParent->removeChild($this)
            ? $newParent->addChild($this)
            : false;
    }

    /**
     * Attaches child related to this model by AuthItemChild.
     * @param AuthItem $item child.
     * @return integer whether child is attached.
     */
    public function addChild(AuthItem $item)
    {
        if ($item->isNewRecord && !$item->save()) {
            return false;
        }
        return Yii::$app->authManager->addChild($this, $item);
    }

    /**
     * Detaches child related to this model by AuthItemChild.
     * @param AuthItem $item child.
     * @return integer whether child is detached.
     */
    public function removeChild($item)
    {
        return Yii::$app->authManager->removeChild($this, $item);
    }
}
