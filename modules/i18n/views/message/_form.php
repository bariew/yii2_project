<?php
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\i18n\models\Message $model
 */
?>
<?php $form = \yii\widgets\ActiveForm::begin();  ?>
    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'language', ['errorOptions' => ['encode' => false]])->label(false)->dropDownList($model::languageList()) ?>
    <?php endif; ?>
    <?= $form->field($model, 'translation')->label(false)->widget(\vova07\imperavi\Widget::class,  ['settings' => [
        'styles' => false,
        'direction' => (\app\modules\common\helpers\HtmlHelper::isRtl() ? 'rtl' : 'ltr'),
        'paragraphize' => false, 'replaceDivs' => false, 'linebreaks' => true,
        'plugins' => ['imagemanager'],
        //'imageUpload' => \yii\helpers\Url::to(['image-upload']),
    ]]) ?>
    <div class="form-group well text-right">
        <?= \yii\helpers\Html::submitButton(Yii::t('modules/i18n', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
<?php $form::end(); ?>