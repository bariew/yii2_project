<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\nalog\models\Source */

$this->title = $model->name;
\yii\web\YiiAsset::register($this);
?>
<div class="source-view">

    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'description:ntext',
        ],
    ]) ?>

</div>
