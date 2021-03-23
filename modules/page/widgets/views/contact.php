<?php
/** @var \app\modules\page\models\ContactForm $model */
?>
<?php $form = \yii\bootstrap4\ActiveForm::begin()  ?>
<?= $form->field($model, 'message')->textarea() ?>
<button class="btn btn-success" type="submit"><?= Yii::t('modules/page', 'Submit') ?></button>
<?php $form::end() ?>
