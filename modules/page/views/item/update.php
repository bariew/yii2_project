<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\page\models\Page $model
 */

$this->title = Yii::t('page', 'Update Page: ') . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('page', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('page', 'Update');
?>

<div class="row">
    <div class="col-md-3 well">
        <?= \Yii::$app->controller->menu; ?>
    </div>
    <div class="col-md-9">
        <div class="item-update">
            <?php echo $this->render('_form', ['model' => $model,]) ?>
        </div>
    </div>
</div>