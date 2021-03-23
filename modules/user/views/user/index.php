<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\user\models\UserSearch $searchModel
 */

$this->title = Yii::t('modules/user', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <p>
        <?php echo Html::a(Yii::t('modules/user', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'email:email',
            'username',
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
