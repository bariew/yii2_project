<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Category */

$this->title = Yii::t('post', 'Create Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('post', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">



    <?= $this->render('form', [
        'model' => $model,
    ]) ?>

</div>
