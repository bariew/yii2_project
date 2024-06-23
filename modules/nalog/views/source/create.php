<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\nalog\models\Source */

$this->title = Yii::t('models/nalog', 'Create Source');
?>
<div class="source-create">

    <?= $this->render('_form', ['model' => $model,]) ?>

</div>
