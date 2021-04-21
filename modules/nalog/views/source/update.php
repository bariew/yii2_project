<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\nalog\models\Source */

$this->title = Yii::t('models/nalog', 'Update Source: {name}', ['name' => $model->name,]);
?>
<div class="source-update">

    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', ['model' => $model,]) ?>

</div>
