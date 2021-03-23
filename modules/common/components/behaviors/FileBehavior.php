<?php
/**
 * FileBehavior class file.
 */

namespace app\modules\common\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 *
 * @property ActiveRecord $owner
 */
class FileBehavior extends Behavior
{
    /**
     * @var string alias for resulting file path
     */
    public $target = '@webroot/files/{user_id}/{model}/{attribute}/{id}/{prefix}.{ext}';
    /**
     * @var string attribute name for uploaded file
     */
    public $attribute;
    /**
     * @var boolean whether to delete the file (equal either 1 or 0)
     */
    public $delete;
    /**
     * @var UploadedFile
     */
    public $file;
    /**
     * @var array
     */
    public $imageSettings = [];
    /**
     * @var Callable[] custom callbacks for events
     */
    public $events = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_merge([
            ActiveRecord::EVENT_AFTER_FIND => function () { $this->setAttributeValue(); },
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_AFTER_VALIDATE => function () { $this->setAttributeValue(); },
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => function () { $this->deleteFile(); }
        ], $this->events);
    }

    /**
     * @return null|string
     */
    protected function setAttributeValue()
    {
        return $this->owner->{$this->attribute} = $this->getFile(null, true);
    }

    /**
     *
     */
    public function beforeValidate()
    {
        if ($this->filesToDelete()) {
            $this->deleteFile();
        }
        $this->file = $this->owner->{$this->attribute} = UploadedFile::getInstance($this->owner, $this->attribute);
    }

    /**
     *
     */
    public function afterSave()
    {
        if (!is_object($this->file)) {
            return;
        }
        $this->saveFile($this->file->tempName, $this->file->extension);
        $this->setAttributeValue();
    }

    /**
     * @param $sourcePath
     * @param $extension
     * @return bool
     * @throws \yii\base\Exception
     */
    public function saveFile($sourcePath, $extension)
    {
        $this->deleteFile();
        $path = str_replace(['{ext}'], [$extension], $this->targetFilePath());
        $dir = dirname($path);
        FileHelper::createDirectory($dir, 0777);
        if (!@copy($sourcePath, $path)) {
            $this->owner->addError($this->attribute,
                Yii::t('app', 'Unable to move the {file} to target directory', ['file' => $this->attribute]));
            return false;
        }
        foreach ($this->imageSettings as $name => $options) {
            $this->processImage($path, $name, $options);
        }
        return true;
    }

    /**
     * Creates image copies processed with options
     * @param $originalPath
     * @param string $field thumbnail name.
     * @param array $options processing options.
     * @throws \yii\base\InvalidConfigException
     */
    private function processImage($originalPath, $field, $options)
    {
        $resultPath = $this->targetFilePath($field);
        switch ($options['method']) {
            case 'thumbnail' :
                \yii\imagine\Image::thumbnail($originalPath, $options['width'], $options['height'])
                    ->save($resultPath, ['format' => pathinfo($originalPath, \PATHINFO_EXTENSION)]);
                break;
        }
    }

    /**
     * returns file path to organisation image dir, unless web_url is set to true, in which case it gives a url.
     * @param bool $url
     * @return string
     */
    protected function getDir($url = false)
    {
        $dir = dirname($this->targetFilePath());
        if ($url) {
            return Url::base(YII_ENV == 'prod' ? 'https' : 'http') . str_replace(Yii::getAlias('@webroot'), '', $dir);
        }
        return $dir;
    }

    /**
     * Gets the file path or url
     * checks for image file in organisation image dir with whatever file extension
     * @param null $prefix
     * @param bool $url
     * @return null|string
     */
    public function getFile($prefix = 'original', $url = false)
    {
        if ($this->owner->isNewRecord) {
            return null;
        }
        $path = $this->targetFilePath($prefix);
        if (!file_exists(dirname($path))) {
            return null;
        }
        $result = @FileHelper::findFiles(dirname($path), ['only' => "/{$prefix}.*"])[0];
        return $url
            ? str_replace(Yii::getAlias('@webroot'), '', $result)
            : $result;
    }

    /**
     * Deletes image file with varying extension from organisation image dir
     * @param null $filename
     * @return bool
     */
    public function deleteFile($filename = null)
    {
        $file = $this->getFile($filename);
        return !is_file($file) || !file_exists($file) || unlink($file);
    }

    /**
     * Gets target file path
     * @param string $prefix
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function targetFilePath($prefix = 'original')
    {
        return Yii::getAlias(str_replace(
            ['{user_id}', '{model}', '{attribute}', '{id}', '{prefix}', '{ext}'],
            [$this->owner->getAttribute('user_id') ? : Yii::$app->user->id, $this->owner->formName(), $this->attribute, $this->owner->primaryKey, $prefix , @$this->file->extension],
            is_callable($this->target) ? call_user_func($this->target, $this->owner) : $this->target
        ));
    }

    /**
     * Generates tmp file path
     * @param null $name
     * @return string
     */
    public function tmpName($name = null)
    {
        $dir = sys_get_temp_dir();
        FileHelper::createDirectory($dir, 0777);
        return $name
            ? $dir . DIRECTORY_SEPARATOR . basename($name)
            : tempnam($dir, Inflector::slug(Yii::$app->name, '_'));
    }

    /**
     * Whether we going to delete files
     * @return bool
     */
    public function filesToDelete()
    {
        return $this->delete;
    }


    // GETTERS

    /**
     * @inheritDoc
     */
    public function hasProperty($name, $checkVars = true)
    {
        if (in_array($name, [$this->attribute, $this->attribute.'_delete'])) {
            return true;
        }
        return parent::hasProperty($name, $checkVars); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritDoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if (in_array($name, [$this->attribute, $this->attribute.'_delete'])) {
            return true;
        }
        return parent::canGetProperty($name, $checkVars); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritDoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        if (in_array($name, [$this->attribute, $this->attribute.'_delete'])) {
            return true;
        }
        return parent::canSetProperty($name, $checkVars);
    }

    /**
     * @inheritDoc
     */
    public function __isset($name)
    {
        return in_array($name, [$this->attribute, $this->attribute.'_delete']) || parent::__isset($name);
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        if ($name == $this->attribute) {
            return $this->file;
        } else if ($name == $this->attribute.'_delete') {
            return $this->delete;
        } else {
            return parent::__get($name);
        }
    }

    /**
     * @inheritDoc
     */
    public function __set($name, $value)
    {
        if ($name == $this->attribute) {
            $this->file = $value;
        } else if ($name == $this->attribute.'_delete') {
            $this->delete = $value;
        } else {
            parent::__set($name, $value);
        }
    }
}
