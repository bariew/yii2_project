<?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

<?= $form->field($model, 'content')->widget(\kartik\editors\Summernote::className()) ?>

<?= $form->field($model, 'visible')->checkbox() ?>

