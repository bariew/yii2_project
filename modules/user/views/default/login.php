<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\modules\user\models\forms\Login $model
 */
$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">


    <p><?= Yii::t('user', 'Please fill out the following fields to login:') ?></p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],

    ]); ?>

    <?php echo $form->field($model, 'email') ?>

    <?php echo $form->field($model, 'password')->passwordInput() ?>

    <?php echo $form->field($model, 'rememberMe', [
        'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ])->checkbox() ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= '';//yii\authclient\widgets\AuthChoice::widget(['baseAuthUrl' => ['auth']]) ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?php echo Html::a(Yii::t('user', 'Forgot password'), ['password-forgot'], ['class' => 'btn btn-default', 'name' => 'reset-button']) ?>
            <?php echo Html::a(Yii::t('user', 'Register'), ['register'], ['class' => 'btn btn-success', 'name' => 'register-button']) ?>
            <?php echo Html::submitButton(Yii::t('user', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
