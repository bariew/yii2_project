<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Item */

$this->title = Yii::t('post', 'Create {modelClass}', [
    'modelClass' => 'Item',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('post', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-create">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
