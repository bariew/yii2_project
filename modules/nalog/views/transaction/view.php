<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\nalog\models\Transaction */

$this->title = Yii::t('nalog', "Transaction");
\yii\web\YiiAsset::register($this);
?>
<div class="transaction-view">

    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            \app\modules\common\helpers\GridHelper::listFormat($model, 'type', ['visible' => true]),
            \app\modules\common\helpers\GridHelper::listFormat($model, 'source_id'),
            'date:date',
            ['attribute' => 'amount', 'format' => ['currency', $model->currency]],
            'description',
        ],
    ]) ?>

</div>
