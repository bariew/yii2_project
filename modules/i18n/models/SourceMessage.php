<?php
/**
 * SourceMessage class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace app\modules\i18n\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\console\controllers\MessageController;
use yii\console\controllers\MigrateController;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;


/**
 * Description.
 *
 * Usage:
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 * @property integer $id
 * @property string $category
 * @property string $message
 *
 * @property Message[] $messages
 */
class SourceMessage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%source_message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['category'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('modules/i18n', 'ID'),
            'category'   => Yii::t('modules/i18n', 'Category'),
            'message'    => Yii::t('modules/i18n', 'Message'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $data = Message::find()->andWhere(['id' => $this->id])->exists()
                ? []
                : array_map(function($v) { return [$v, $this->id]; }, Message::languageList());
            Message::getDb()->createCommand()
                ->batchInsert(Message::tableName(), ['language', 'id'], $data)
                ->execute();
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['id' => 'id'])
            ->from(['messages' => Message::tableName()])
            ->indexBy('language');
    }

    /**
     * Gets all used categories list.
     * @return array source category list
     */
    public static function categoryList()
    {
        $data = self::find()->orderBy('category')->groupBy(['category'])->select(['category'])->column();
        return array_combine($data, $data);
    }
}
