<?php
/** @var \yii\widgets\ActiveForm $form */
/** @var \app\modules\page\models\Page $model */
; ?>

<?php echo $form->field($model, 'seo_title')->textInput(['maxlength' => 255])
    ->hint(\yii\helpers\Html::a(Yii::t('i18n', 'translations'), $model->translationLink('seo_title'))) ?>


<?php echo $form->field($model, 'seo_description')->textInput(['maxlength' => 255])
    ->hint(\yii\helpers\Html::a(Yii::t('i18n', 'translations'), $model->translationLink('seo_description'))) ?>

<?php echo $form->field($model, 'seo_keywords')->textInput(['maxlength' => 255])
    ->hint(\yii\helpers\Html::a(Yii::t('i18n', 'translations'), $model->translationLink('seo_keywords'))) ?>

<?php echo $form->field($model, 'layout')->textInput(['maxlength' => 255])
    ->hint(\yii\helpers\Html::a(Yii::t('i18n', 'translations'), $model->translationLink('content'))) ?>

