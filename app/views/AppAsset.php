<?php
/**
 * @link http://www.yiiframework.com/
 */

namespace app\views;

use yii\web\AssetBundle;

/**
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@webroot/themes/default';
    public $css = [
        'css/main.css',
        'css/glyphicon.css',
    ];
    public $js = [
        'css/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
