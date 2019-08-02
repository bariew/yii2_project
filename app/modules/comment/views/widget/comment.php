<?php 
use yii\widgets\ActiveForm;
use yii\helpers\Html;
; ?>
<div class="comment-form">

    <?php $form = ActiveForm::begin(['id' => 'comment-form']); ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('modules/comment', 'Create') : Yii::t('modules/comment', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
