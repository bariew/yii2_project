<?php
/**
 * ImageGallery class file.
 */

namespace app\modules\product\widgets;

use yii\base\Widget;
use app\modules\product\models\Item;

/**
 * For rendering image gallery for post Item model.
 *
 */
class ImageGallery extends Widget
{
    /**
     * @var Item $model
     */
    public $model;

    /**
     * @var string thumbnail name (like thumbnail1, thumbnail2)
     * @see FileBehavior::imageSettings() keys
     */
    public $field;

    /**
     * @var string view file name.
     */
    public $viewName = 'image_gallery';

    public $admin = true;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render($this->viewName, [
            'model' => $this->model,
            'items' => [$this->model->getFile($this->field)],
            'admin' => $this->admin
        ]);
    }
}