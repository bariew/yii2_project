<?php
/**
 * Item class file.
 */

namespace app\modules\post\models;

use Yii;
use \yii\db\ActiveRecord;
use app\modules\common\components\behaviors\FileBehavior;
use app\modules\post\components\CategoryToItemBehavior;
use yii\db\ActiveQuery;

/**
 * Description.
 *
 * Usage:
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $brief
 * @property string $content
 * @property integer $is_active
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $image
 *
 * @mixin FileBehavior
 * @mixin CategoryToItemBehavior
 *
 */
class Item extends ActiveRecord
{
    const SCENARIO_ADMIN = 'admin';
    const SCENARIO_USER = 'user';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%post_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'brief', 'content'], 'required'],
            [['is_active'], 'boolean'],
            [['brief', 'content'], 'string'],
            [['title'], 'string', 'max' => 255],
            ['image', 'image', 'maxFiles' => 10],
            [['categoryIds'], 'safe', 'on' => [static::SCENARIO_ADMIN, static::SCENARIO_USER]],
            ['user_id', 'required', 'on' => static::SCENARIO_ADMIN],
            ['user_id', 'integer', 'on' => static::SCENARIO_ADMIN],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            'image' => [
                'class' => FileBehavior::className(),
                'attribute' => 'image',
                'imageSettings' => [
                    'thumb1' => ['method' => 'thumbnail', 'width' => 50, 'height' => 50],
                    'thumb2' => ['method' => 'thumbnail', 'width' => 100, 'height' => 100],
                    'thumb3' => ['method' => 'thumbnail', 'width' => 200, 'height' => 200],
                ]
            ],
            'categoryToItem' => [
                'class' => CategoryToItemBehavior::className()
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        switch ($this->scenario) {
            case static::SCENARIO_USER :
                $this->user_id = Yii::$app->user->id;
                break;
            case static::SCENARIO_DEFAULT:
                $this->is_active = 1;
                break;
        }
    }

    /**
     * Gets search query according to model scenario.
     * @param array $params search params key=>value
     * @return ActiveQuery
     */
    public function search($params = [])
    {
        return $this::find()->andFilterWhere(array_merge($this->attributes, $params));
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('post', 'ID'),
            'user_id' => Yii::t('post', 'User ID'),
            'title' => Yii::t('post', 'Title'),
            'brief' => Yii::t('post', 'Brief'),
            'content' => Yii::t('post', 'Content'),
            'is_active' => Yii::t('post', 'Is Active'),
            'created_at' => Yii::t('post', 'Created At'),
            'updated_at' => Yii::t('post', 'Updated At'),
            'image' => Yii::t('post', 'Image'),
            'categoryIds' => Yii::t('post', 'Category list'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public static function active()
    {
        return static::find()->andWhere(['is_active' => 1]);
    }

    /**
     * is_active field available values.
     * @return array
     */
    public static function activeList()
    {
        return [
            0 => Yii::t('post', 'No'),
            1 => Yii::t('post', 'Yes'),
        ];
    }
}
