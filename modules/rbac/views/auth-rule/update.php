<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\AuthRule */
$this->title = Yii::t('modules/rbac', 'Update {modelClass}: ', [
            'modelClass' => 'Auth Rule',
        ]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('modules/rbac', 'Auth Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = Yii::t('modules/rbac', 'Update');
?>
<div class="auth-rule-update">
    <?= $this->render('_form', ['model' => $model,]) ?>
</div>