<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\common\models\Settings $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="settings-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->dropDownList(\app\modules\common\models\Settings::nameList()) ?>
    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
