<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\comment\models\Item */

$this->title = Yii::t('modules/comment', 'Update {modelClass}: ', [
    'modelClass' => 'Item',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('modules/comment', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('modules/comment', 'Update');
?>
<div class="item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
