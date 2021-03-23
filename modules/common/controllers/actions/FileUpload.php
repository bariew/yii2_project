<?php
/**
 * FileUpload class file.
 */

namespace app\modules\common\controllers\actions;


use app\modules\common\components\behaviors\FileMultipleBehavior;
use yii\base\Action;
use Yii;
use yii\helpers\Html;
use yii\web\UploadedFile;

/**
 * Uploads videos via WYSIWYG editor
 * returns link with printscreen inserted in WYSIWYG
 */
class FileUpload extends Action
{
    /**
     * @var Callable
     */
    public $getModelCallback;
    /**
     * @var string
     */
    public $attribute = 'files';

    /**
     * Uploads videos for Summernote WYSIWYG editor
     * @param $id
     * @param string $name
     * @return string
     */
    public function run($id = null, $name = '')
    {
        $model = call_user_func($this->getModelCallback, $id);
        $link = '';
        if ($model->load(Yii::$app->request->post()) && $model->validate([$this->attribute])) {
            /** @var FileMultipleBehavior $behavior */
            $behavior = $model->getBehavior($this->attribute);
            $file = UploadedFile::getInstance($model, $this->attribute); //the uploaded video file
            $path = $behavior->tmpName();
            $file->saveAs($path); // do not delete the uploaded file on the end of request as php does
            $link = Html::a((strlen($name) ? $name : $file->name), basename($path), ['data-filename' => $file->name]);
            Yii::$app->session->addFlash('success', Yii::t('file', 'Successfully uploaded'));
        }
        return $this->controller->renderAjax('//views/_file_upload', ['model' => $model, 'link' => $link, 'attribute' => $this->attribute]);
    }
}
