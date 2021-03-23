<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\nalog\models\Transaction */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transaction-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList($model::typeList()) ?>

    <?= $form->field($model, 'source_id')->dropDownList($model->sourceList()) ?>

    <?= $form->field($model, 'date')->widget(\kartik\datecontrol\DateControl::class, \app\modules\common\helpers\DateHelper::dateControlOptions()); ?>

    <div class="form-inline form-group row">
        <?= $form->field($model, 'amount', ['options' => ['class' => 'form-group col-sm-4 d-inline']])->label(false)->textInput([
                'placeholder' => Yii::t('app', 'Amount'), 'style' => ['min-width' => '300px']
        ]) ?>
        <?= $form->field($model, 'currency', ['options' => ['class' => 'form-group col-sm-2 d-inline']])->label(false)->widget(\kartik\select2\Select2::class, [
            'data' => $model::currencyList(),
            'language' => Yii::$app->language,
            'options' => ['prompt' => Yii::t('app','Currency'), 'value' => ($model->isNewRecord ? ($model->currency ? : $model->source->currency) : $model->currency)],
        ]) ?>
        <?= Html::button(Yii::t('modules/nalog', 'Convert to RUB'), [
            'class' => 'btn btn-success d-inline',
            'data-url' => \yii\helpers\Url::to(['convert']),
            'onclick' => <<<JS
$('#transaction-description').val($('#transaction-description').val()+$('#transaction-amount').val()+$('#transaction-currency').val()).trigger('change');
$.get($(this).data('url'), {
    amount:$('#transaction-amount').val(), 
    currency:$('#transaction-currency').val(),
    date:$('#transaction-date').val()
}, function(value) {
    $('#transaction-amount').val(value).trigger('change');
    $('#transaction-currency').val('RUB').trigger('change');
})
JS
        ]); ?>
    </div>

    <?= $form->field($model, 'description')->textarea(); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('models/nalog', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
