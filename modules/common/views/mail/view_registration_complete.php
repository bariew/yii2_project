<?php
use yii\helpers\Url;
/** @var \app\modules\user\models\forms\Register $model */
echo Yii::t('mail', 'registration_complete_confirm_{link}', [
    'link' => Url::to(['email-confirm', 'email' => $model->email, 'code' => $model->confirmationCode()], true)
]);
