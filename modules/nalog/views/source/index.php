<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\nalog\models\SourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('models/nalog', 'Sources');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-index">

    <?= Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-dark btn-sm float-right rounded-circle', 'data-toggle' => "ajax-modal"]) ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'summary' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            \app\modules\common\helpers\GridHelper::listFormat($searchModel, 'type'),
            'description:ntext',
            ['class' => 'yii\grid\ActionColumn', 'options' => ['style' => 'width:90px']],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
