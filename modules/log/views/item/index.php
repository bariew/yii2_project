<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\helpers\GridHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\log\models\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('log', 'Events');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'link:raw',
            GridHelper::dateFormat($searchModel, 'created_at', ['format'=> 'datetime']),
            GridHelper::listFormat($searchModel, 'user_id'),
            'event',
            'message:raw',
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {delete}'],
        ],
    ]); ?>

</div>
