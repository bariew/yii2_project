<?php
/**
 * FileInput class file.
 */

namespace app\modules\common\widgets;

use yii\helpers\Html;

/**
 * Description.
 *
 * Usage:
 */
class FileInput extends \kartik\file\FileInput
{
    /**
     * File delete attribute name for setting it to true when image delete button is clicked
     * Set it to false to hide
     * @var string|bool
     */
    public $deleteAttribute;
    public $uploadedFile;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $attribute = str_replace('[]', '', $this->attribute);
        $plugin_options = [
            'showUpload' => false,
            'layoutTemplates' => ['footer' => ''],
        ];
        $this->uploadedFile = $this->uploadedFile ?? $this->model->$attribute;
        $this->deleteAttribute = ($this->deleteAttribute === null)
            ? $attribute . '_delete'
            : $this->deleteAttribute;
        if (isset($this->options['value'])) {
            $this->uploadedFile = $this->options['value'];
        }
        foreach ((array) $this->uploadedFile as $path => $url) {
            if (!$url) {
                continue;
            }
            $path = is_numeric($path) ? \Yii::getAlias('@webroot/'.$url) : $path;
            $size = file_exists($path) ? round(filesize($path)/(1024*1024), 2).' Mb' : '';
            $name = basename($path);
            $preview = preg_match('#\.(jpg|jpeg|gif|png)#', $path)
                ? "<img src='{$url}' title='{$name}' class='file-preview-image' style='max-width:160px;height:auto;'>"
                : "<a href='{$url}' title='{$name}' target='_blank'>".$name."</a>";
            $plugin_options['initialPreview'][] = $preview . '<div class="file-thumbnail-footer">' .
                '    <div class="file-footer-caption" title="'.$name.'">'.$size.'</div>' .
                '</div>';
        }

        if ($this->deleteAttribute) {
            // check deleteAttribute checkbox on image close/delete event
            $updateSelector = Html::getInputId($this->model, $this->attribute);
            $deleteSelector = Html::getInputId($this->model, $this->deleteAttribute);
            $this->view->registerJs("
                $('#".$updateSelector."').on('fileclear', function(event) {
                    $('#".$deleteSelector."').val(1);
                });
            ");
        } else {
            // do not show delete/close buttons if image is not uploaded
            $plugin_options['showRemove'] = $plugin_options['showClose'] = false;
        }

        $this->pluginOptions = array_merge_recursive($plugin_options, $this->pluginOptions);
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getInput($type, $list = false)
    {
        // adds deleteAttribute input for checking on delete
        $add = ($this->deleteAttribute ? Html::activeHiddenInput($this->model, $this->deleteAttribute, ['value' => 0]) : '');
        return parent::getInput($type, $list) . $add;
    }
}
