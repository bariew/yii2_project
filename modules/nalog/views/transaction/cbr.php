<?php

use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\nalog\models\CurrencyHistory */
/** @var $data array|\app\modules\nalog\models\CurrencyHistory[] */

$this->title = Yii::t('models/nalog', 'Cbr');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cbr-index">

    <h2><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin(['method' => 'get', 'options' => [
        'class' => 'form-inline'
    ]]); ?>

    <?= $form->field($model, 'date')->label(false)->widget(DateRangePicker::class, [
        'language' => Yii::$app->language == 'en' ? 'en-US' : Yii::$app->language,
        'pluginOptions' => ['locale' => ['format' => 'YYYY-MM-DD']],
        'options' => ['placeholder' => $model->getAttributeLabel('date')]
    ]) ?>
    <?= $form->field($model, 'select')->label(false)->widget(\kartik\select2\Select2::class, [
        'data' => array_diff(array_combine($model->attributes(), $model->attributes()), ['date']),
        'options' => ['multiple' => true, 'placeholder' => $model->getAttributeLabel('select')]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('models/nalog', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?= \app\modules\common\widgets\apexcharts\ApexCharts::widget($data); ?>

</div>
