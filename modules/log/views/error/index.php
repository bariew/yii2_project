<?php

use yii\grid\GridView;
use app\modules\log\models\ErrorSearch;
use app\modules\common\helpers\GridHelper;
$this->title = Yii::t('modules/log', 'Errors');
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var ErrorSearch $searchModel
 */
?>
<?= \yii\helpers\Html::a('<i class="glyphicon glyphicon-trash"></i>'
    . Yii::t('modules/log', 'delete all'),
    ['delete-all'],
    ['class' => 'btn btn-danger float-right'])
?>
<br /><br />
<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        GridHelper::listFormat($searchModel, 'level'),
        'category',
        GridHelper::dateFormat($searchModel, 'log_time', ['format' => 'datetime']),
        [
            'attribute' => 'message',
            'format' => 'raw',
            'value' => function($data) { return \yii\helpers\StringHelper::truncate($data->message, 170);}
        ],
        ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {delete}', 'options' => ['width' => '50px']],
    ],
]); ?>