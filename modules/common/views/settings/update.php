<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\common\models\Settings $model */

$this->title = Yii::t('common', 'Update Settings: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="settings-update">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
