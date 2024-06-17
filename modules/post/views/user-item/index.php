<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\post\models\SearchItem */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('post', 'Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">


    <p>
        <?= Html::a(
            Yii::t('post', 'Create Item'), 
            ['create'], 
            ['class' => 'btn btn-success']
        ) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],   
            'id',
            'title',
            'is_active:boolean',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel, 
                    'attribute' => 'created_at', 
                    'dateFormat' => 'php:Y-m-d',
                    'options' => ['class' => 'form-control']
                ]),
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
