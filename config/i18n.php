<?php
/**
 * Console .yii/message command config
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

return [
    'sourcePath' => dirname(__DIR__),
    'messagePath' => dirname(__DIR__).'/messages',
    'languages' => Yii::$app->params['languages'],
    'translator' => 'Yii::t',
    'sort' => true,
    'overwrite' => true,
    'removeUnused' => false,
    'markUnused' => false,
    'except' => [
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/messages',
        '/web',
        '/tests',
        '/runtime',
        '/migrations',
        '.idea',
    ],
    'only' => ['*.php'],
    'phpFileHeader' => '',
    'format' => 'db',
   // 'db' => 'db_prod',
    'sourceMessageTable' => '{{%source_message}}',
    'messageTable' => '{{%message}}'
];
