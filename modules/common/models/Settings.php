<?php
/**
 * Settings class file
 */

namespace app\modules\common\models;

use app\modules\common\components\behaviors\EncryptBehavior;
use app\modules\common\components\behaviors\SerializeBehavior;
use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $value
 */
class Settings extends \yii\db\ActiveRecord
{
    const NAME_OAUTH_GOOGLE = 'oauth_google';
    const NAME_OAUTH_MICROSOFT = 'oauth_microsoft';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%common_settings}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['value'], 'safe'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'encrypt' => ['class' => EncryptBehavior::class, 'attributes' => ['value']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', 'Name'),
            'value' => Yii::t('common', 'Value'),
        ];
    }

    /**
     * @param $name
     * @param null $value
     * @return self
     */
    public static function findOrCreate($name, $value = null)
    {
        $result = static::findOne(['name' => $name]) ?: new static(['name' => $name]);
        if (isset($value)) {
            $result->value = $value;
            $result->save();
        }
        return $result;
    }

    /**
     * @return array
     */
    public static function nameList()
    {
        return [
            static::NAME_OAUTH_GOOGLE => Yii::t('common', 'Google Oauth Key'),
            static::NAME_OAUTH_MICROSOFT => Yii::t('common', 'Microsoft Oauth Key'),
        ];
    }
}
