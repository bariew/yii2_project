<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\product\models\Item */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'attachments[]')
        ->widget(\app\widgets\FileInput::className(), [
            'options' => ['multiple' => true],
            'uploadedFile' => $model->attachments,
            'deleteAttribute' => 'attachments_delete',
        ]); ?>
    <?= \app\modules\product\widgets\ImageGallery::widget(['model' => $model, 'field' => 'thumb1']); ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'brief')->widget(\app\widgets\Summernote::className(), [
        'imageDeleteUrl' => \yii\helpers\Url::to(['image-delete', 'id' => $model->id]),
        'fileUploadUrl' => \yii\helpers\Url::to(['file-upload', 'id' => $model->id]),
        'options' => ['id' => 'editor', 'class' => 'form-control'],
    ]) ?>

    <?= $form->field($model, 'content')->widget(\kartik\editors\Summernote::className()) ?>
    
    <?= $form->field($model, 'is_active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('product', 'Create') : Yii::t('product', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
