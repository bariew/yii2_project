<?php
/**
 * CategoryToItemBehavior class file.
 *
 */

namespace app\modules\product\components;
use app\modules\product\models\Category;
use app\modules\product\models\CategoryToItem;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Description of CategoryToItemBehavior
 * @property ActiveRecord $owner
 *
 */
class CategoryToItemBehavior extends \yii\base\Behavior 
{
    protected $_categoryIds;
    
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }
    
    public function getCategoryIds()
    {
        if ($this->_categoryIds !== null) {
            return $this->_categoryIds;
        }
        return $this->_categoryIds = CategoryToItem::find()
            ->select(['category_id'])
            ->where(['item_id'=>$this->owner->id])->column();
    }
    
    public function setCategoryIds($ids)
    {
        $this->_categoryIds = $ids;
    }
    
    public function allCategoryList()
    {
        return ArrayHelper::map(Category::active()->asArray()->all(), 'id', 'name');
    }
    
    public function getCategoryToItem()
    {
        return $this->owner->hasMany(CategoryToItem::className(), ['item_id' => 'id']);
    }
    
    public function getCategories()
    {
        return $this->owner->hasMany(Category::className(), ['id' => 'category_id'])
            ->via('categoryToItem');
    }
    
    public function deleteRelations()
    {
        return CategoryToItem::deleteAll(['item_id' => $this->owner->id]);
    }
    
    public function addRelations($categoryIds)
    {
        if (!$categoryIds) {
            return true;
        }
        $rows = [];
        foreach ($categoryIds as $categoryId) {
            $rows[] = [$this->owner->id, $categoryId];
        }
        return \Yii::$app->db->createCommand()->batchInsert(
            CategoryToItem::tableName(),
            ['item_id', 'category_id'], 
            $rows
        )->execute();
    }
    
    public function afterSave()
    {
        if ($this->_categoryIds === null) {
            return true;
        }
        $this->deleteRelations();
        $this->addRelations($this->_categoryIds);        
    }
    
    public function afterDelete()
    {
        $this->deleteRelations();
    }
}
