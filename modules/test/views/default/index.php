
<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\test\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('post', 'Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">


    <p>
        <?= Html::a(
            Yii::t('test', 'Create Item'),
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
                'format' => 'boolean',
                'attribute' => 'is_active',
                'filter' => Html::activeDropDownList($searchModel, 'is_active', $searchModel::activeList())
            ],
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
