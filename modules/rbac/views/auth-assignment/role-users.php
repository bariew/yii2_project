<?php
/** @var \app\modules\rbac\models\AuthItem $role */
; ?>
<?= \kartik\select2\Select2::widget(['model' => $role, 'attribute' => 'users', 'data' => $users, 'options' => [
    'multiple' => true,
    'onchange'=>'$.post("/rbac/auth-assignment/role-users?name=' . $role->name . '", {
        "ids" : $(this).val()
    });'
]]); ?>
