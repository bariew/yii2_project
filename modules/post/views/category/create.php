<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Category */

$this->title = Yii::t('post', 'Create Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('post', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('form', [
        'model' => $model,
    ]) ?>

</div>
