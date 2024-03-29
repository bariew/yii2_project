<?php

use yii\widgets\ActiveForm;
use app\modules\rbac\models\AuthItem;
use \yii\helpers\Html;
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var AuthItem $model
 * @var yii\widgets\ActiveForm $form
 */

    $form = ActiveForm::begin();
?>
<?php foreach ($actions as $moduleName => $controllerActions): ?>
    <h4><?= $moduleName; ?></h4>
    <ul>
        <?php foreach ($controllerActions as $controllerName => $actions): ?>
            <li>
                <h5><?= Html::checkbox($controllerName, !array_diff($actions, $children), [
                        'onchange' => 'var t = $(this); t.parents("li").find("input").each(function(){'
                            . 'if($(this).is(":checked") != t.is(":checked")){'
                                . '$(this).click(); '
                            . '}'
                        . '})'
                    ]) . ' ' .$controllerName; ?></h5>        
                <ul>
                    <?php foreach ($actions as $action): ?>
                        <li>
                            <label>
                                <?= Html::checkbox($action, in_array($action, $children), [
                                    'onchange' => '$.post($(this).data("href")+$(this).is(":checked")*1, {})',
                                    'data-href' => Url::to([
                                        'auth-item-child/add', 
                                        'parent_id'=>$parent->name, 
                                        'id' => $action,
                                        'add' => ''
                                    ])
                                ]) . ' ' . $action ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>