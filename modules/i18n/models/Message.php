<?php
/**
 * Message class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace app\modules\i18n\models;

use app\modules\common\helpers\DateHelper;
use Yii;
use yii\db\ActiveRecord;

/**
 * Description.
 *
 * Usage:
 * @author Pavel Bariev <bariew@yandex.ru>
 * @property integer $id
 * @property string $language
 * @property string $translation
 * @property string $updated_at
 *
 * @property SourceMessage $source
 */
class Message extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['translation', 'sourceMessage', 'sourceCategory'], 'string'],
            ['language', 'unique', 'targetAttribute' => ['id', 'language'], 'message' => 'Translation for this language already exists'],
            [['language', 'translation'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => Yii::t('modules/i18n', 'ID'),
            'language'       => Yii::t('modules/i18n', 'Language'),
            'sourceMessage'  => Yii::t('modules/i18n', 'Message source'),
            'sourceCategory' => Yii::t('modules/i18n', 'Message category'),
            'translation'    => Yii::t('modules/i18n', 'Translation'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        $this->updated_at = DateHelper::now();
        return parent::beforeSave($insert);
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->flush();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(SourceMessage::className(), ['id' => 'id'])
            ->from(['source' => SourceMessage::tableName()]);
    }

    /**
     * Возвращает сообщение для перевода из связанной модели.
     *
     * @return string
     */
    public function getSourceMessage()
    {
        return $this->source->message;
    }

    /**
     * Возвращает категорию перевода из связанной модели.
     *
     * @return string
     */
    public function getSourceCategory()
    {
        return $this->source->category;
    }

    /**
     * @return array
     */
    public static function languageList()
    {
        $config = require \Yii::getAlias('@app/config/i18n.php');
        return array_combine($config['languages'], $config['languages']);
    }
}
