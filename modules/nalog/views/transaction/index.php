<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\nalog\models\TransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('models/nalog', 'Transactions');
$hideOptions = array_fill_keys(['headerOptions', 'filterOptions','contentOptions', 'footerOptions'],['class' => Yii::$app->request->get('fullscreen') ? '' : 'd-none']);
?>
<div class="transaction-index">

    <?= \app\modules\common\helpers\HtmlHelper::button() ?>

    <h2><?= Html::encode($this->title) ?></h2>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'summary' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'showFooter' => true,
        'options' => ['class' => 'paper grid-view text-right bg-white shoadow-sm p-2'],
        'pager' => ['class' => \yii\bootstrap4\LinkPager::class, 'options' => ['class' => 'float-right']],
        'columns' => [
            \app\modules\common\helpers\GridHelper::listFormat($searchModel, 'type', $hideOptions),
            \app\modules\common\helpers\GridHelper::listFormat($searchModel, 'tax_type', $hideOptions),
            \app\modules\common\helpers\GridHelper::listFormat($searchModel, 'source_id'),
            \app\modules\common\helpers\GridHelper::dateRangeFormat($searchModel, 'date', $hideOptions),
            ['attribute' =>'amount', 'footer' => "TOTAL: ". $dataProvider->query->sum('amount')],
            array_merge(['attribute' => 'description'], $hideOptions),
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions' => ['data-toggle' => "ajax-modal", 'class' => 'text-dark'],
                'header' =>
                    Html::a('<i class="glyphicon glyphicon-fullscreen"></i>', \yii\helpers\Url::current([
                        'fullscreen' => !Yii::$app->request->get('fullscreen')
                    ]), ['class' => 'text-dark btn']),
                'options' => ['style' => 'width:90px']
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
