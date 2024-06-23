<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\modules\user\models\forms\Login $model
 */
$this->title = 'Register';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">


    <p>Please fill out the following fields to login:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'register-form',
        'options' => ['class' => 'form-horizontal'],
    ]); ?>
    
    <?php echo $form->field($model, 'email') ?>
    
    <?php echo $form->field($model, 'username') ?>
        
    <?php echo $form->field($model, 'password')->passwordInput() ?>
    
    <?php echo $form->field($model, 'password_repeat')->passwordInput() ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?php echo Html::submitButton(
                \Yii::t('user', 'Register'),
                ['class' => 'btn btn-primary', 'name' => 'register-button']
            ) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
