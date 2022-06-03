<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Item */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'image[]')->fileInput(['multiple' => true]) ?>
    <?= \app\modules\post\widgets\ImageGallery::widget(['model' => $model, 'field' => 'thumb1']); ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'user_id')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'categoryIds')->widget(Select2::className(), [
        'data' => $model->allCategoryList(),
        'options' => [
            'placeholder' => Yii::t('post', 'Select categories'),
            'multiple' => true,
            'class' => 'form-control',
        ]
    ]);?>
    <?= $form->field($model, 'brief')->widget(\kartik\editors\Summernote::className()) ?>
    <?= $form->field($model, 'content')->widget(\kartik\editors\Summernote::className()) ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('post', 'Create') : Yii::t('post', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
