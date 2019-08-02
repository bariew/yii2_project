<?php
/**
 * Category class file.
 */

namespace app\modules\post\models;

use app\modules\post\components\NestedQuery;
use app\components\behaviors\FileBehavior;
use creocoder\nestedsets\NestedSetsBehavior;
use Yii;
use yii\db\ActiveQuery;

/**
 *
 * @property integer $id
 * @property string $title
 * @property string $name
 * @property string $content
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $is_active
 * @property Item[] $items
 *
 * @mixin NestedSetsBehavior
 * @mixin FileBehavior
 */
class Category extends \yii\db\ActiveRecord
{
    const SCENARIO_ADMIN = 'admin';

    public $items = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post_category';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        switch ($this->scenario) {
            case static::SCENARIO_ADMIN:

                break;
            default : $this->is_active = 1;
        }
    }

    /**
     * @return NestedQuery
     */
    public static function find()
    {
        return new NestedQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['is_active'], 'integer'],
            [['title', 'name'], 'string', 'max' => 255],
            ['image', 'image', 'maxFiles' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_ADMIN => ['content', 'title', 'is_active', 'name', 'image'],
            static::SCENARIO_DEFAULT => []
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'tree' => ['class' => NestedSetsBehavior::className()],
            'image' => [
                'class' => FileBehavior::className(),
                'attribute' => 'image',
                'imageSettings' => [
                    'thumb1' => ['method' => 'thumbnail', 'width' => 50, 'height' => 50],
                    'thumb2' => ['method' => 'thumbnail', 'width' => 100, 'height' => 100],
                    'thumb3' => ['method' => 'thumbnail', 'width' => 200, 'height' => 200],
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('modules/post', 'ID'),
            'title' => Yii::t('modules/post', 'Title'),
            'name' => Yii::t('modules/post', 'Name'),
            'content' => Yii::t('modules/post', 'Content'),
            'lft' => Yii::t('modules/post', 'Lft'),
            'rgt' => Yii::t('modules/post', 'Rgt'),
            'depth' => Yii::t('modules/post', 'Depth'),
            'is_active' => Yii::t('modules/post', 'Is Active'),
            'image' => Yii::t('modules/post', 'Image'),
        ];
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        if ($this->depth == 0) {
            throw new \BadMethodCallException(
                Yii::t('modules/post', "Root category can not be deleted.")
            );
        }
        return parent::beforeDelete();
    }

    /**
     * @param array $params
     * @return NestedQuery
     */
    public function search($params = [])
    {
        return static::find()->andFilterWhere(array_merge($this->attributes, $params));
    }

    /**
     * is_active available value list.
     * @return array
     */
    public static function activeList()
    {
        return [
            0 => Yii::t('modules/post', 'No'),
            1 => Yii::t('modules/post', 'Yes'),
        ];
    }
    /**
     * @return array
     */
    public function transactions()
    {
        return [
            static::SCENARIO_DEFAULT => static::OP_ALL,
        ];
    }

    /**
     * @return NestedQuery
     */
    public static function active()
    {
        return static::find()->andWhere(['is_active'=>1]);
    }

    /**
     * @param $attributes
     * @return int
     */
    public function updateChildren($attributes)
    {
        $childrenIds = $this->children()->select(['id'])->column();
        return $this->updateAll($attributes, ['id' => $childrenIds]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategoryToItems()
    {
        return static::hasMany(CategoryToItem::className(), ['category_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getItems()
    {
        return static::hasMany(Item::className(), ['id' => 'item_id'])
            ->via('categoryToItems');
    }

    /**
     * @param $items
     * @return array
     */
    public static function activeItems($items)
    {
        $result = [];
        $exclude = [];
        foreach ($items as $k => $item) {
            if (!$item['is_active']) {
                $exclude[] = $item;
                continue;
            }
            if (static::isChildOfArray($exclude, $item)) {
                continue;
            }
            $result[$k] = $item;
        }
        return $result; // keys are kept!
    }

    /**
     * @param $items
     * @return array
     */
    public static function toTree($items)
    {
        $result = [];
        $parents = [];
        foreach ($items as $item) {
            $parents[$item['depth']][$item['lft']] = $item;
            if (!isset($parents[$item['depth']-1])) {
                $resultEnd = end($result);
                if (!$resultEnd || ($resultEnd['depth'] == $item['depth'])) {
                } else if ($resultEnd['depth'] > $item['depth']) {
                    $result = []; // We will unset previous result if its depth more than current
                } else {
                    continue; // we will not include current to result as its depth more that results roots
                }
                $result[$item['lft']] = &$parents[$item['depth']][$item['lft']];
                continue;
            }
            $parent = end($parents[$item['depth']-1]);
            if (!static::isChildOfArray([$parent], $item)) {
                continue;
            }
            $key = key($parents[$item['depth']-1]);
            $parents[$item['depth']-1][$key]['items'][$item['lft']]
                = &$parents[$item['depth']][$item['lft']];
        }
        return $result;
    }

    /**
     * @param $parents
     * @param $child
     * @return bool
     */
    public static function isChildOfArray($parents, $child)
    {
        foreach ($parents as $parent) {
            if ($child['lft'] > $parent['lft']
                && $child['rgt'] < $parent['rgt']) {
                return true;
            }
        }
        return false;
    }
}
