<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\nalog\models\SourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('models/nalog', 'Sources');
?>
<div class="source-index">

    <?= \app\modules\common\helpers\HtmlHelper::button() ?>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'summary' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'paper grid-view text-end bg-white shoadow-sm p-2'],
        'pager' => ['class' => \yii\bootstrap5\LinkPager::class, 'options' => ['class' => 'float-right']],
        'columns' => [
            'name',
            \app\modules\common\helpers\GridHelper::listFormat($searchModel, 'type'),
            'description:ntext',
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions' => ['data-bs-toggle' => "ajax-modal", 'class' => 'text-dark'],
                'options' => ['style' => 'width:90px']
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
