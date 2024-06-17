<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\common\models\Settings $model */

$this->title = Yii::t('common', 'Create Settings');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-create">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
