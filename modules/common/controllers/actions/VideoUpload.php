<?php
/**
 * VideoUpload class file.
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
class VideoUpload extends Action
{
    const VIDEO_SCREENSHOT_POSTFIX = '_video_screenshot';

    /**
     * @var Callable
     */
    public $getModelCallback;
    /**
     * @var string
     */
    public $attribute = 'videos';

    /**
     * Uploads videos for Summernote WYSIWYG editor
     * @param $id
     * @return string
     */
    public function run($id = null)
    {
        $model = call_user_func($this->getModelCallback, $id);
        $link = '';
        if ($model->load(Yii::$app->request->post()) && $model->validate([$this->attribute])) {
            /** @var FileMultipleBehavior $behavior */
            $behavior = $model->getBehavior($this->attribute);
            $video = UploadedFile::getInstance($model, $this->attribute); //the uploaded video file
            $videoPath = $behavior->tmpName();
            $video->saveAs($videoPath); // do not delete the uploaded file on the end of request as php does
            $screenshotPath = $behavior->tmpName() . '.png';
            $playButtonPath = Yii::getAlias('@webroot/themes/dashboard/images/play_button.png');
            //                             make printscreen on 1 second                                      scale for output printscreen; add the play button overlay                            save output to screenshot
            ConsoleCommand::create("ffmpeg -ss 00:00:01 -i {$videoPath} -i $playButtonPath -filter_complex \"[0:v]scale=400:-1[bg];[bg][1:v]overlay=main_w/2-overlay_w/2:main_h/2-overlay_h/2\" -vframes 1 -q:v 2 {$screenshotPath}")->run();
            $link = HtmlHelper::a(
                Html::img('data:image/png;base64,' . base64_encode(file_get_contents($screenshotPath)), [
                    'data-filename' => pathinfo($video->name, PATHINFO_FILENAME) . static::VIDEO_SCREENSHOT_POSTFIX . '.png'
                ]),
                basename($videoPath),
                ['data-filename' => $video->name]
            );
            Tools::addFlash('success', 'Successfully uploaded');
        }
        return $this->controller->renderAjax('//common/_video_upload', ['model' => $model, 'link' => $link, 'attribute' => $this->attribute]);
    }
}
