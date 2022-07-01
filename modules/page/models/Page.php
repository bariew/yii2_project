<?php
/**
 * Item class file.
 */

namespace app\modules\page\models;

use app\modules\common\components\ActiveRecordCachedTrait;
use app\modules\common\components\behaviors\FileBehavior;
use app\modules\common\helpers\FileHelper;
use app\modules\i18n\behaviors\ModelTranslateBehavior;
use bariew\nodeTree\ARTreeBehavior;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property integer $pid
 * @property integer $rank
 * @property string $title
 * @property string $brief
 * @property string $content
 * @property string $name
 * @property string $url
 * @property string $layout
 * @property integer $visible
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 *
 * @property Page[] $children
 * @mixin ARTreeBehavior
 * @mixin ModelTranslateBehavior
 */
class Page extends ActiveRecord
{
    use ActiveRecordCachedTrait;

    const VISIBLE_YES = 1;
    const VISIBLE_NO = 0;
    public static $currentPage = false;
    private static $_all;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{page}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'visible'], 'default', 'value' => 1],
            [['title'], 'required', 'except'=>'nodeTree'],
            [['name'], 'required', 'except'=>'nodeTree', 
                'when' => function() { return $this->pid; },
                'whenClient' => 'function($attribute, $value) { return false; }'
            ],
            [['pid', 'rank', 'visible'], 'integer'],
            [['brief', 'content', 'seo_description', 'seo_keywords'], 'string'],
            [['title', 'name', 'url', 'layout', 'seo_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('page', 'ID'),
            'pid' => Yii::t('page', 'Pid'),
            'rank' => Yii::t('page', 'Rank'),
            'title' => Yii::t('page', 'Title'),
            'brief' => Yii::t('page', 'Brief'),
            'content' => Yii::t('page', 'Content'),
            'name' => Yii::t('page', 'Path'),
            'url' => Yii::t('page', 'Url'),
            'layout' => Yii::t('page', 'Layout'),
            'visible' => Yii::t('page', 'Visible'),
            'seo_title' => Yii::t('page', 'SEO Title'),
            'seo_description' => Yii::t('page', 'SEO Description'),
            'seo_keywords' => Yii::t('page', 'SEO Keywords'),

        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'nodeTree' => [
                'class'         => ARTreeBehavior::className(),
                'actionPath'    => '/page/item/update'
            ],
            'translate' => [
                'class' => ModelTranslateBehavior::class,
                'attributes' => ['title', 'content', 'seo_title', 'seo_description', 'seo_keywords']
            ],
        ];
    }

    /**
     * Gets a page by the url.
     * @param string $url
     * @return bool|null|static
     */
    public static function getCurrentPage($url = '')
    {
        if (static::$currentPage !== false) {
            return static::$currentPage;
        }
        /** @var static $model */
        $model = static::find()->where([
            'url' => preg_replace('/[\/]{2,}/', '/', '/' . $url . '/'),
            'visible' => static::VISIBLE_YES
        ])->orderBy('id DESC')->one();
        if (!$model) {
            return static::$currentPage = null;
        }
        if ($model->layout) {
            Yii::$app->controller->layout = $model->layout;
        }
        Yii::$app->view->title = $model->seo_title;
        Yii::$app->view->registerMetaTag(['name' => 'description', 'content' => $model->seo_description]);
        Yii::$app->view->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords]);
        return static::$currentPage = $model;
    }

    /**
     * @return array|ActiveRecord[]|static[]
     */
    public static function all()
    {
        return static::$_all = static::$_all ?? static::find()->indexBy('name')->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(static::class, ['pid' => 'id'])->alias('children');
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (!$this->pid) {
            throw new Exception("Can not delete the root page");
        }
        if (!parent::beforeDelete()) {
            return false;
        }
        foreach ($this->children as $child) {
            $child->delete();
        }
        return true;
    }
}
