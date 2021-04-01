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
        'options' => ['prompt' => Yii::t('modules/nalog','Default Currency')],
    ]) ?>

    <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
        <?= Html::activeRadioList($model, 'type', $model::typeList(), [
            'class' => 'btn-group btn-group-toggle', 'data-toggle' => 'buttons',
            'item' => function ($index, $label, $name, $checked, $value) {
                return Html::label(Html::radio($name, $checked, ['value' => $value]) . $label, null, [
                    'class' => 'btn btn-sm ' . ($checked ? 'active ' : '')
                        . ($index == 0 ? 'btn-outline-success' : 'btn-outline-danger')
                ]);
            }]); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('models/nalog', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
