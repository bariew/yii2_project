<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\nalog\models\Transaction */

$this->title = Yii::t('models/nalog', 'Create Transaction');
?>
<div class="transaction-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
