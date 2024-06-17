<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\product\models\Category */

$this->title = Yii::t('product', 'Create Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">



    <?= $this->render('form', [
        'model' => $model,
    ]) ?>

</div>
