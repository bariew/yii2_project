<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\nalog\models\TransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('models/nalog', 'Transactions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-index">

    <?= Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-dark btn-sm float-right rounded-circle', 'data-toggle' => "ajax-modal"]) ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'summary' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'showFooter' => true,
        'columns' => [
            \app\modules\common\helpers\GridHelper::listFormat($searchModel, 'type', \app\modules\common\helpers\GridHelper::HIDE_OPTIONS),
            \app\modules\common\helpers\GridHelper::listFormat($searchModel, 'tax_type', \app\modules\common\helpers\GridHelper::HIDE_OPTIONS),
            \app\modules\common\helpers\GridHelper::listFormat($searchModel, 'source_id'),
            \app\modules\common\helpers\GridHelper::dateRangeFormat($searchModel, 'date', \app\modules\common\helpers\GridHelper::HIDE_OPTIONS),
            [
                'attribute' =>'amount',
                'format' => 'raw',
                'value' => function (\app\modules\nalog\models\Transaction $model) {
                    return Html::tag('span', $model->amount, [
                        'class' => ($model->source->type == \app\modules\nalog\models\Source::TYPE_INCOME ? 'text-success' : 'text-danger')
                    ]);
                },
                'footer' => "TOTAL: ". array_sum(array_map(function (\app\modules\nalog\models\Transaction $v) {
                    return $v->source->type == \app\modules\nalog\models\Source::TYPE_INCOME ? $v->amount : -$v->amount;
                }, $dataProvider->models)),
            ],
            array_merge(['attribute' => 'description'], \app\modules\common\helpers\GridHelper::HIDE_OPTIONS),
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Html::a('<i class="glyphicon glyphicon-fullscreen"></i>', '#', [
                    'class' => 'float-right',
                    'onclick' => <<<JS
    $('.hidden').toggleClass('d-none')  
JS
                ]),
                'options' => ['style' => 'width:90px']
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
