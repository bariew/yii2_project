<?php
/**
 * CallableValidator class file.
 */

namespace app\modules\common\components\validators;

use yii\validators\ValidationAsset;
use yii\validators\Validator;

/**
 * Use your custom callback to add validation error
 * @example
`
    in model
    public function rules()
    {
        ...
        ['email', CallableValidator::class, 'callback' => function ($user, $attribute) { return $user->$attribute != 'my@email.com'; }]
    }
`
 */
class CallableValidator extends Validator
{
    public $callback;
    public $clientCallback;

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (!call_user_func_array($this->callback, [$model, $attribute, $this])) {
            $this->addError($model, $attribute, $this->message);
        }
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        if ($this->clientCallback) {
            ValidationAsset::register($view);
        }
        return $this->clientCallback;
    }
}
