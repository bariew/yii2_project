<?php
/**
 * ARTreeAssets class file.
 */

namespace app\modules\rbac\components\nodetree;
use yii\web\AssetBundle;

/**
 * Description:
 */
class ARTreeAssets extends AssetBundle
{
    public $sourcePath = '@app/modules/rbac/components/nodetree/views/jsTree';
    public $js = [
        'src/jstree.js',
        'src/jstree.dnd.js',
        'src/jstree.search.js',
        'src/jstree.types.js',
        'src/jstree.state.js',
        'src/jstree.contextmenu.js',
        'src/jstree.checkbox.js',
        'artree.js'
    ];
    public $css = [
        'src/themes/default/style.css',
        'artree.css'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}