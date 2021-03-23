<?php
$mainConfig = require 'web.php';
return [
    'sourcePath' => __DIR__ . '/..',
    'messagePath' => __DIR__ . '/../messages',
    'languages' => array_keys($mainConfig['params']['languages']),
    'translator' => 'Yii::t',
    'sort' => true,
    'overwrite' => true,
    'removeUnused' => true,
    'markUnused' => false,
    'except' => [
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
    ],
    'only' => ['*.php'],
    'format' => 'php',
    'exclude'=>array('/messages', '/runtime', '/tests', '/migrations', '/config', '/modules'),

    // Connection component ID for "db" format.
    //'db' => 'db',
];
