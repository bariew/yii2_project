<?php
/**
 * VideoChatAsset class file
 */

namespace app\modules\common\widgets\videochat;

use yii\web\AssetBundle;

/**
 * Class VideoChatAsset
 */
class VideoChatAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/assets';
    public $css = [
        'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.3/css/fontawesome.min.css'
    ];
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/7.3.0/adapter.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js',
        'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.3/css/fontawesome.min.css',
        'chat.js','events.js','helpers.js',
    ];
}