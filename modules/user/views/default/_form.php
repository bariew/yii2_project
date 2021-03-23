<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\modules\user\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
?>
<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
    
    <?php echo $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>

    <?php echo $form->field($model, 'company_name')->textInput(['maxlength' => 255]) ?>

    <?php echo $form->field($model, 'password')->passwordInput(['maxlength' => 255]) ?>

    <?php echo $form->field($model, 'api_key')->textInput(['maxlength' => 255]) ?>

    <?php if ($clients = array_diff_key(\app\modules\user\models\Auth::clientCollection()->getClients(), $model->auths)): ?>
        <div class="form-group">
            <label><?= Yii::t('user/default/update', 'Attach social accounts') ?></label>
            <?= yii\authclient\widgets\AuthChoice::widget(['baseAuthUrl' => ['auth-attach'], 'clients' => $clients]) ?>
        </div>
    <?php endif ?>
    <?php if ($clients = array_intersect_key(\app\modules\user\models\Auth::clientCollection()->getClients(), $model->auths)): ?>
        <div class="form-group">
            <label><?= Yii::t('user/default/update', 'Detach attached social accounts') ?></label>
            <?= yii\authclient\widgets\AuthChoice::widget(['baseAuthUrl' => ['auth-detach'], 'clients' => $clients]) ?>
        </div>
    <?php endif ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
