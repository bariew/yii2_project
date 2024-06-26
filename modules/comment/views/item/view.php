<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\comment\models\Comment */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('comment', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-view">



    <p>
        <?= Html::a(Yii::t('comment', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('comment', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('comment', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'parent_class',
            'parent_id',
            'branch_id',
            'content:ntext',
            'created_at',
            'updated_at',
            'active',
        ],
    ]) ?>
</div>
