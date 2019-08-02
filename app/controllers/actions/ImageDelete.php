<?php
/**
 * ImageDelete class file.
 */

namespace app\controllers\actions;


use app\components\behaviors\FileMultipleBehavior;
use yii\base\Action;
use Yii;

/**
 * Deletes images inserted in WYSIWYG
 */
class ImageDelete extends Action
{
    /**
     * @var Callable
     */
    public $getModelCallback;
    /**
     * @var string
     */
    public $attribute = 'images';
    /**
     * @var string
     */
    public $videoAttribute = 'videos';

    /**
     * Uploads videos for Summernote WYSIWYG editor
     * @param $id
     * @return string
     */
    public function run($id = null)
    {
        $model = call_user_func($this->getModelCallback, $id);
        /** @var FileMultipleBehavior $behavior */
        $behavior = $model->getBehavior($this->attribute);
        $name = Yii::$app->request->post('name');
        $behavior->deleteFiles($name, null, false);
        if (strpos($name, VideoUpload::VIDEO_SCREENSHOT_POSTFIX)) { // if deleting video screenshot - delete the video as well
            $behavior = $model->getBehavior($this->videoAttribute);
            $videoName = str_replace(VideoUpload::VIDEO_SCREENSHOT_POSTFIX, '', pathinfo($name, PATHINFO_FILENAME) . '.');
            $behavior->deleteFiles(array_filter($behavior->getFiles(), function ($v) use ($videoName) {
                return strpos($v, $videoName) === 0; // delete video files matching the image name without extension
            }), null, false);
        }
    }
}
