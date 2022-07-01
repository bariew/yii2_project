<?php
/**
 * ModelTranslateBehavior class file
 */

namespace app\modules\i18n\behaviors;

use app\modules\i18n\models\SourceMessage;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class ModelTranslateBehavior
 *
 * Saves attributes into i18n db tables so you can translate them later.
 * Parent model has the translation via $this->translate($attribute) method
 *
 * @package app\modules\i18n\behaviors
 *
 * @property ActiveRecord $owner
 */
class ModelTranslateBehavior extends Behavior
{
    public $attributes = ['name'];

    /**
     * @inheritDoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => function ($e) {
                foreach ($this->attributes as $attribute) {
                    $model = new SourceMessage(['category' => $this->category($attribute), 'message' => $this->message($attribute)]);
                    $model->save(false);
                }
            },
            ActiveRecord::EVENT_AFTER_DELETE => function ($e) {
                foreach ($this->attributes as $attribute) {
                    SourceMessage::deleteAll(['category' => $this->category($attribute), 'message' => $this->message($attribute)]);
                }
            },
        ];
    }

    /**
     * @param string $attribute
     * @param null $language
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function translate($attribute = 'name', $language = null)
    {
        if (!in_array($attribute, $this->attributes)) {
            return $this->owner->{$attribute};
        }
        $method = 't';
        return \Yii::$method($this->category($attribute), $this->message($attribute), [], ($language ?? \Yii::$app->language));
    }

    /**
     * @param string $attribute
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function translationLink($attribute = 'name')
    {
        return ['/i18n/message/index', 'SourceMessageSearch[message]' => $this->message($attribute), 'SourceMessageSearch[category]' => $this->category($attribute)];
    }

    /**
     * @param null $attribute
     * @return string|string[]
     * @throws \yii\base\InvalidConfigException
     */
    private function category($attribute)
    {
        return str_replace(['{{', '%', '}}'], ['', '', ''], $this->owner->tableName()) . "_{$attribute}";
    }

    /**
     * @param $attribute
     * @return string
     */
    private function message($attribute)
    {
        return $this->category($attribute)."_".$this->owner->primaryKey;
    }
}
