<?php
/**
 * @link http://www.yiiframework.com/
 */

namespace app\modules\common\views;

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
        'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
