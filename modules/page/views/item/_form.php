<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\modules\page\models\Item $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?php echo \yii\bootstrap4\Tabs::widget([
        'items' => [
            [
                'label' => 'Content',
                'content' => $this->render('_formContent', compact('form', 'model')),
                'active' => true
            ],
            [
                'label' => 'Settings',
                'content' => $this->render('_formSettings', compact('form', 'model'))
            ],
        ],
    ]); ?>

    <div class="form-group">
        <?php echo Html::submitButton(
            $model->isNewRecord ? Yii::t('modules/page', 'Create') : Yii::t('modules/page', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
