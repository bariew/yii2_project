<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\nalog\models\Transaction */

$this->title = Yii::t('models/nalog', 'Update Transaction: {name}', ['name' => $model->id,]);
?>
<div class="transaction-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
