<?php

use app\modules\common\models\Settings;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('common', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-index">



    <p>
        <?= Html::a(Yii::t('common', 'Create Settings'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name',
            [
                'class' => ActionColumn::className(),
                'template' => '{link} {delete}',
                'buttons' => ['link' => function ($url, Settings $model) {
                    return Html::a('<i class="glyphicon glyphicon-link"></i>', ['/user/oauth/'.str_replace('oauth_', '', $model->name)]);
                }],
                'visibleButtons' => [
                    'link' => function (Settings $model) { return in_array($model->name, [$model::NAME_OAUTH_GOOGLE, $model::NAME_OAUTH_MICROSOFT]); },
                ],
            ],
        ],
    ]); ?>


</div>
