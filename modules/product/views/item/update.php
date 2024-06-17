<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\product\models\Item */

$this->title = Yii::t('product', 'Update {modelClass}: ', [
    'modelClass' => 'Item',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('product', 'Update');
?>
<div class="item-update">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
