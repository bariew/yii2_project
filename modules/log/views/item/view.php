<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\log\models\Log */

$this->title = Yii::t('log', 'View Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('log', 'Events'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-view">

    <p>
        <?= Html::a(Yii::t('log', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('log', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('log', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'user_id',
            'event',
            'link',
            'message:ntext',
            'created_at:datetime',
        ],
    ]) ?>

</div>
