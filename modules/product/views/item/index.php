
<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\product\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('product', 'Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">


    <p>
        <?= Html::a(
            Yii::t('product', 'Create Item'), 
            ['create'], 
            ['class' => 'btn btn-success']
        ) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'user_id',
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
