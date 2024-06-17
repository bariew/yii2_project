<?php
/** @var \yii\web\View $this */
/** @var \app\modules\rbac\models\AuthItem $model */
$this->title = $model->isNewRecord
    ? Yii::t('rbac', 'Create Auth Item')
    : Yii::t('rbac', 'Edit "{name}"', ['name' => $model->name])
?>
<div class="row">
    <div class="col-md-3 well">
        <?= \Yii::$app->controller->menu; ?>
    </div>
    <div class="col-md-9">
    <?php
    $items = [[
        'label' => Yii::t('modules/rbac', 'Settings'),
        'content' => $this->render('_form', compact('model')),
    ]];
    if (!$model->isNewRecord) {
        $items = array_merge($items, [
            [
                'label' => Yii::t('modules/rbac', 'Users'),
                'url' => ['auth-assignment/role-users', 'name' => $model->name],
                'visible'   => false
            ],
            [
                'label' => Yii::t('modules/rbac', 'Permissions'),
                'url' => ['auth-item-child/tree', 'name' => $model->name],
                'visible'   => false
            ],
        ]);
    }

    echo \yii\jui\Tabs::widget([
        'items' => $items,
        'options' => ['tag' => 'div'],
        'itemOptions' => ['tag' => 'div'],
        'headerOptions' => ['class' => 'my-class'],
        'clientOptions' => ['collapsible' => false],
    ]); ?>
    </div>
</div>
