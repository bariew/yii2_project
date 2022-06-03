<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\page\models\Page $model
 */

$this->title = Yii::t('page', 'Create Page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('page', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-3 well">
        <?= \Yii::$app->controller->menu; ?>
    </div>
    <div class="col-md-9">
        <div class="item-create">
            <h1><?php echo Html::encode($this->title) ?></h1>
            <?php echo $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>