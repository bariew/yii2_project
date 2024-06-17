<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\comment\models\Comment */

$this->title = Yii::t('comment', 'Create {modelClass}', [
    'modelClass' => 'Item',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('comment', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-create">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
