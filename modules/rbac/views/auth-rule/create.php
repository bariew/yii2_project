
<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\AuthRule */
$this->title = Yii::t('modules/rbac', 'Create {modelClass}', [
        'modelClass' => 'Auth Rule',
    ]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('modules/rbac', 'Auth Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-rule-create">
    <?= $this->render('_form', ['model' => $model,]) ?>
</div>