<?php

namespace app\modules\comment\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $parent_class
 * @property integer $parent_id
 * @property integer $branch_id
 * @property string $content
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $active
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            ['user_id', 'filter', 'filter' => function(){
                return $this->isNewRecord ? \Yii::$app->user->id : $this->user_id;
            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('comment', 'ID'),
            'user_id' => Yii::t('comment', 'User ID'),
            'parent_class' => Yii::t('comment', 'Parent Class'),
            'parent_id' => Yii::t('comment', 'Parent ID'),
            'branch_id' => Yii::t('comment', 'Branch ID'),
            'content' => Yii::t('comment', 'Content'),
            'created_at' => Yii::t('comment', 'Created At'),
            'updated_at' => Yii::t('comment', 'Updated At'),
            'active' => Yii::t('comment', 'Active'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() 
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }
}
