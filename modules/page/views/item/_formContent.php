<?php
/** @var \yii\widgets\ActiveForm $form */
/** @var \app\modules\page\models\Page $model */
; ?>
<?= $form->field($model, 'title')->textInput(['maxlength' => 255])
    ->hint(\yii\helpers\Html::a(Yii::t('i18n', 'translations'), $model->translationLink('title'))) ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => 255])->hint(Yii::t('page', 'Shown in url')) ?>

<?= $form->field($model, 'content')->widget(\vova07\imperavi\Widget::class)
    ->hint(\yii\helpers\Html::a(Yii::t('i18n', 'translations'), $model->translationLink('content'))) ?>

<?= $form->field($model, 'visible')->checkbox() ?>

