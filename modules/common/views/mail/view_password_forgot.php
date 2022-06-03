<?php

use yii\helpers\Url;
/** @var \app\modules\user\models\Login $model */

echo Yii::t('mail', 'password_reset_{link}', [
    'link' => Url::to(['password-reset', 'email' => $model->email, 'code' => $model->resetCode()], true)
]);
