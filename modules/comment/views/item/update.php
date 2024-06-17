<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\comment\models\Comment */

$this->title = Yii::t('comment', 'Update {modelClass}: ', [
    'modelClass' => 'Item',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('comment', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('comment', 'Update');
?>
<div class="item-update">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
