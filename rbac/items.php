<?php

return [
    'guest' =>  [
        'type' => \yii\rbac\Item::TYPE_ROLE,
        'description' => 'Guest',
        'data' => NULL,
    ],
    'user' =>  [
        'type' => \yii\rbac\Item::TYPE_ROLE,
        'description' => 'User',
        'data' => NULL,
        'children' =>  ['guest'],
    ],
    'admin' =>  [
        'type' => \yii\rbac\Item::TYPE_PERMISSION,
        'description' => 'Administrator Role',
        'data' => NULL,
        'children' =>  ['user'],
    ],
];
