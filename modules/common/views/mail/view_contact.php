<?php
/** @var \app\modules\page\models\ContactForm $model */
echo Yii::t('mail', 'contact_{message}_{email}', ['message' => $model->message, 'email' => $model->email]);
