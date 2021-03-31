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

    <?= $form->field($model, 'source_id', [
        'template' => "{label}"
            .Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/nalog/source/create'], ['class' => 'btn btn-sm', 'title' => Yii::t('app', 'Add')])
            ."\n{input}\n{hint}\n{error}"
    ])->dropDownList($model->sourceList()) ?>

    <?= $form->field($model, 'date')->widget(\kartik\datecontrol\DateControl::class, \app\modules\common\helpers\DateHelper::dateControlOptions()); ?>

    <div class="form-inline form-group">
        <?= $form->field($model, 'amount', ['options' => ['class' => 'form-group col-6 d-inline']])->label(false)->textInput([
            'placeholder' => Yii::t('app', 'Amount'), 'style' => ['width' => 'inherit']
        ]) ?>
        <?= $form->field($model, 'currency', ['options' => ['class' => 'form-group col-4 d-inline']])->label(false)->widget(\kartik\select2\Select2::class, [
            'data' => $model::currencyList(),
            'language' => Yii::$app->language,
            'options' => ['prompt' => Yii::t('app','Currency'), 'value' => ($model->isNewRecord ? ($model->currency ? : $model->source->currency) : $model->currency)],
        ]) ?>
        <?= Html::button('<i class="glyphicon glyphicon-refresh p-1"></i>', [
            'class' => 'btn btn-success form-group',
            'title' => Yii::t('modules/nalog', 'Conver to RUB'),
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
