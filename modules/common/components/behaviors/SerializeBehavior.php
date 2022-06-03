<?php
/**
 * SerializeBehavior class file.
 */

namespace app\modules\common\components\behaviors;
use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * ActiveRecord behavior for serializing/json encoding
 * array data before saving it to db
 *
 * Usage: set in model
    public function behaviors()
    {
        return [
            ['class' => 'app\modules\common\components\behaviors\SerializeBehavior', 'attributes' => ['myAttribute']]
       ];
    }
 *
 * @property ActiveRecord $owner
 */
class SerializeBehavior extends Behavior
{
    const TYPE_JSON = 'json';
    const TYPE_PHP = 'php';
    const TYPE_IMPLODE = 'implode';

    public $attributes = [];
    public $type = self::TYPE_JSON;
    public $when;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT => 'unserializeAttributes',
            ActiveRecord::EVENT_AFTER_FIND => 'unserializeAttributes',
            ActiveRecord::EVENT_BEFORE_INSERT => 'serializeAttributes',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'serializeAttributes',
            ActiveRecord::EVENT_AFTER_INSERT => 'unserializeAttributes',
            ActiveRecord::EVENT_AFTER_UPDATE => 'unserializeAttributes',
        ];
    }

    /**
     * Serializes data
     * @throws Exception
     */
    public function serializeAttributes()
    {
        if (!$this->when()) {
            return;
        }
        foreach ($this->attributes as $key => $attribute) {
            $attribute = is_numeric($key) ? $attribute : $key;
            $value = $this->owner->getAttribute($attribute);
            switch ($this->type) {
                case static::TYPE_JSON :
                    $value = json_encode($value);
                    break;
                case static::TYPE_PHP :
                    $value = serialize($value);
                    break;
                case static::TYPE_IMPLODE :
                    $value = '|' . implode('|', array_filter((array)$value)) . '|';
                    break;
                default: throw new Exception("Unknown type: ". $this->type);
            }
            // Front-end does not like dealing with "" values
            if (in_array($value, ['""'], true)) {
                $value = null;
            }
            $this->owner->setAttribute($attribute, $value);
        }
    }

    /**
     * Unserializes data
     * @throws Exception
     */
    public function unserializeAttributes()
    {
        if (!$this->when()) {
            return;
        }
        foreach ($this->attributes as $key => $attribute) {
            $default = is_numeric($key) ? [] : $attribute;
            $attribute = is_numeric($key) ? $attribute : $key;
            $value = $this->owner->getAttribute($attribute);
            if (is_array($value)) {
                continue;
            }
            json_decode(1); // empty json_last_error()
            switch ($this->type) {
                case static::TYPE_JSON :
                    $jsonValue = $value ? json_decode($value, true) : [];
                    $value = json_last_error() ? $value : $jsonValue;
                    break;
                case static::TYPE_PHP :
                    $value = $value ? unserialize($value) : [];
                    break;
                case static::TYPE_IMPLODE:
                    $value = array_filter(explode('|', $value));
                    break;
                default: throw new Exception("Unknown type: ". $this->type);
            }
            $value = (!$value && $this->owner->isNewRecord) ? $default : $value;
            $this->owner->setAttribute($attribute, $value);
        }
    }

    /**
     * @return bool
     */
    private function when()
    {
        return !is_callable($this->when) || call_user_func($this->when);
    }

}
