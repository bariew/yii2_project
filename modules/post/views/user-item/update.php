<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Item */

$this->title = Yii::t('post', 'Update {modelClass}: ', [
    'modelClass' => 'Item',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('post', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('post', 'Update');
?>
<div class="item-update">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
