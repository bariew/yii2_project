<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\product\models\Item */

$this->title = Yii::t('product', 'Create {modelClass}', [
    'modelClass' => 'Item',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-create">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
