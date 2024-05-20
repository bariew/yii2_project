<?php
/**
 * EncryptBehavior class file.
 */

namespace app\modules\common\components\behaviors;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * ActiveRecord behavior for storing encrypted data
 *
 * Usage: set in model
    public function behaviors()
    {
        return [
            ['class' => 'app\modules\common\components\behaviors\EncryptBehavior', 'attributes' => ['myAttribute']]
       ];
    }
 *
 * @property ActiveRecord $owner
 */
class EncryptBehavior extends Behavior
{
    public $attributes = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT => function () { $this->decryptAttributes(); },
            ActiveRecord::EVENT_AFTER_FIND =>  function () { $this->decryptAttributes(); },
            ActiveRecord::EVENT_BEFORE_INSERT =>  function () { $this->encryptAttributes(); },
            ActiveRecord::EVENT_BEFORE_UPDATE =>  function () { $this->encryptAttributes(); },
            ActiveRecord::EVENT_AFTER_INSERT =>  function () { $this->decryptAttributes(); },
            ActiveRecord::EVENT_AFTER_UPDATE =>  function () { $this->decryptAttributes(); },
        ];
    }

    /**
     * @param $attribute
     * @param $value
     * @return int
     */
    public function encrypt($attribute, $value)
    {
        return $this->owner->updateAttributes([$attribute => static::encrypted(json_encode($value))]);
    }

    /**
     *
     */
    private function encryptAttributes()
    {
        foreach ($this->attributes as $attribute) {
            $value = $this->owner->getAttribute($attribute);
            $this->owner->setAttribute($attribute, $value ? static::encrypted(json_encode($value)) : null);
        }
    }

    /**
     *
     */
    private function decryptAttributes()
    {
        foreach ($this->attributes as $key => $attribute) {
            $value = $this->owner->getAttribute($attribute);
            $this->owner->setAttribute($attribute, $value ? json_decode(static::encrypted($value, true), true) : null);
        }
    }

    /**
     * @param $string
     * @param false $decrypt
     * @return false|string
     */
    private static function encrypted($string, $decrypt = false)
    {
        $encrypt_method = "AES-256-CBC";
        $secret_iv = $secret_key = \Yii::$app->params['salt'];
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16); // sha256 is hash_hmac_algo
        return $decrypt
            ? openssl_decrypt($string, $encrypt_method, $key, 0, $iv)
            : openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    }
}
