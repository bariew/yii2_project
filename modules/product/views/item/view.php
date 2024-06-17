<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\product\models\Item */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-view">



    <p>
        <?= Html::a(Yii::t('product', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('product', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('product', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'title',
            'brief:html',
            'content:html',
            'is_active',
            'created_at:datetime',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => \app\modules\product\widgets\ImageGallery::widget(['model' => $model, 'field' => 'thumb1'])
            ],
        ],
    ]) ?>

</div>
