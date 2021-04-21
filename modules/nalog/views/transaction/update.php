<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\nalog\models\Transaction */

$this->title = Yii::t('models/nalog', 'Update Transaction: {name}', ['name' => $model->id,]);
?>
<div class="transaction-update">

    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
