<?php
/**
 * ModelTranslateBehavior class file
 */

namespace app\modules\i18n\behaviors;

use app\modules\i18n\models\SourceMessage;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;
use yii\helpers\Inflector;

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
    public $prefix;
    public $attributes = ['name'];

    /**
     * @inheritDoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => function ($e) { $this->afterSave($e); },
            ActiveRecord::EVENT_AFTER_UPDATE => function ($e) { $this->afterSave($e); },
            ActiveRecord::EVENT_AFTER_DELETE => function ($e) { $this->afterSave($e); },
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
        return \Yii::$method($this->category($attribute), $this->owner->{$attribute}, [], ($language ?? \Yii::$app->language));
    }

    /**
     * @param $attribute
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function isTranslated($attribute)
    {
        return ($this->owner->{$attribute} != $this->translate($attribute)) && $this->translate($attribute);
    }

    /**
     * @var AfterSaveEvent $event
     */
    private function afterSave($event)
    {
        $owner = $this->owner;
        foreach ($this->attributes as $attribute) {
            if (!$value = $owner->getAttribute($attribute)) {
                $value = $attribute."_".$owner->primaryKey;
                $owner->updateAttributes([$attribute => $value]);
            };
            $condition = ['category' => $this->category($attribute), 'message' => $this->getOldAttribute($event, $attribute)];
            $data = ['category' => $this->category($attribute), 'message' => $value];
            switch ($event->name) {
                case ActiveRecord::EVENT_AFTER_INSERT :
                case ActiveRecord::EVENT_AFTER_UPDATE  :
                    $model = SourceMessage::findOne($condition) ? : new SourceMessage($data);
                    $model->message = $owner->getAttribute($attribute);
                    $model->save(false);
                    break;
                case ActiveRecord::EVENT_AFTER_DELETE :
                    SourceMessage::deleteAll($condition);
                    break;
            }
        }
    }

    /**
     * @param $event
     * @param $attribute
     * @return mixed
     */
    private function getOldAttribute($event, $attribute)
    {
        if (isset($event->changedAttributes) && isset($event->changedAttributes[$attribute])) {
            return $event->changedAttributes[$attribute];
        } else if(isset($event->sender->oldAttributes[$attribute])) {
            return $event->sender->oldAttributes[$attribute];
        } else {
            return $event->sender->{$attribute};
        }
    }

    /**
     * @param null $attribute
     * @return string|string[]
     * @throws \yii\base\InvalidConfigException
     */
    private function category($attribute)
    {
        $prefix = $this->prefix ? : str_replace(['{{', '%', '}}'], ['', '', ''], $this->owner->tableName());
        return "{$prefix}_{$attribute}";
    }
}
