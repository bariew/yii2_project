<?php
/**
 * Summernote class file.
 */

namespace app\modules\common\widgets;


use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

/**
 *
 */
class Summernote extends InputWidget
{
    public $imageDeleteUrl;
    public $videoUploadUrl;
    public $fileUploadUrl;
    public $pluginOptions = [];
    public $toolbar = [];
    public function run()
    {
        $id = $this->options['id'];
        $options = array_merge_recursive([
            'disableDragAndDrop' => true,
            'popatmouse' => false, // todo remove when image popover issue is fixed https://github.com/summernote/summernote/issues/2732
            'minHeight' => 300,
            'toolbar' => ($this->toolbar ? : [
                ["font", ["fontname", "style"]],
                ["style", ["bold", "italic", "underline", "clear"]],
                ["fontsize", ["fontsize"]],
                ["color", ["color", "picture", ($this->videoUploadUrl ? 'video2' : ''), ($this->fileUploadUrl ? 'file2' : '')]],
                ["para", ["ul", "ol", "paragraph"]],
                ["undo", ["undo", "redo"]],
                ["code", ["link", "codeview", "fullscreen"]]
            ]),
            'pluginOptions' => [
                'onCreateLink' => new \yii\web\JsExpression(<<<JS
function(originalLink) {
    return originalLink;
}
JS
            )],

            'callbacks' => [
                'onMediaDelete' => new JsExpression(<<<JS
function(target, editor, editable) {
    $.post("{$this->imageDeleteUrl}", {name:target[0].src.split(/[\\/]/).pop()});
    target.remove();
    $(editor).find('a:empty[data-filename]').remove();// if the image is a content of a link - remove the link too (e.g. for an uploaded video prinscreen)
}
JS
),
            ],
            'buttons' => [
                'video2' => new JsExpression(<<<JS
function (context) {
    var button = $.summernote.ui.button({
        contents: '<i class="note-icon-video"/>',
        tooltip: 'Video',
        click: function () {
            context.invoke('editor.saveRange');
            $('#ajaxModal').html('').modal().load("{$this->videoUploadUrl}", function () {
                $('#editorSelector').val('#{$id}');
            });// open video upload modal window, set editorSelector for inserting upload results
            context.invoke('editor.restoreRange');
        }
      });   
      return button.render();
}
JS
                ),
                'file2' => new JsExpression(<<<JS
function (context) {
    var button = $.summernote.ui.button({
        contents: '<i class="note-icon-arrow-circle-down"/>',
        tooltip: 'File',
        click: function () {
            var url = "{$this->fileUploadUrl}";
            url = url + (url.search(/\?/) == -1 ? '?' : '&') + 'name=' + $("#{$id}").summernote('createRange').toString();
            context.invoke('editor.saveRange');
            $('#ajaxModal').html('').modal().load(url, function () {
                $('#editorSelector').val('#{$id}');
            });// open file upload modal window, set editorSelector for inserting upload results
            context.invoke('editor.restoreRange');
        }
      });   
      return button.render();
}
JS
                ),
            ]
        ], $this->pluginOptions);
        if (!isset($this->options['name']) && isset($this->name)) {
            $this->options['name'] = $this->name;
        }
        $jsonOptions = Json::encode($options);
        $this->view->registerJs(<<<JS
$("#{$id}").summernote({$jsonOptions}).next().on('focusout', ".note-codable", function(e) {
    if ($("#{$id}").summernote('codeview.isActivated')) {
        $("#{$id}").val($("#{$id}").summernote('code')); //in the code mode it's not being saved
    }
    return true;
});
JS
        );
        return $this->model
            ? Html::activeTextarea($this->model, $this->attribute, $this->options)
            : Html::textarea($this->name, $this->value, $this->options);
    }
}
