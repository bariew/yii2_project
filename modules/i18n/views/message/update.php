<?php
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\i18n\models\Message $model
 */
$this->title = Yii::t('modules/i18n', '{language} Translation for {source}', [
    'source' => $model->source->message,
    'language' => @Yii::$app->params['languages'][$model->language]
]);
?>
<?= $this->render('_form', ['model' => $model]); ?>