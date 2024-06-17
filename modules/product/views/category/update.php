<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\product\models\Category */

$this->title = Yii::t('product', 'Update {modelClass}: ', [
    'modelClass' => 'Category',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('product', 'Update');
?>
<div class="category-update">



    <?= $this->render('form', [
        'model' => $model,
    ]) ?>

</div>
