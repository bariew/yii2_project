<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\nalog\models\Source */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="source-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'currency')->label(false)->widget(\kartik\select2\Select2::class, [
        'data' => $model::currencyList(),
        'language' => Yii::$app->language,
        'options' => ['prompt' => Yii::t('nalog','Default Currency')],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('models/nalog', 'Save'), ['class' => 'btn btn-success float-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
