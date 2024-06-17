<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Category */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('post', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-view">



    <p>
        <?= Html::a(Yii::t('post', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('post', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('post', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => \app\modules\post\widgets\ImageGallery::widget(['model' => $model, 'field' => 'thumb1'])
            ],
            'id',
            'title',
            'name',
            'content:ntext',
            'is_active',
        ],
    ]) ?>

</div>
