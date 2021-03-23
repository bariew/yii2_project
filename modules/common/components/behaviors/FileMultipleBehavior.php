<?php
/**
 * Created by PhpStorm.
 * User: pt
 * Date: 10.11.16
 * Time: 12:38
 */

namespace app\modules\common\components\behaviors;


use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * Class FileMultipleBehavior
 *
 * Automatically manages ActiveRecord attached multiple files
 *
 * Send multiple files to model file validation rules and the behavior will do the rest
 * Get your files with $model->getFiles() method
 *
 * @example
 *
public function behaviors()
{
    return [
        'attachments' => [
            'class' => FileMultipleBehavior::class,
            'target' =>   function ($model) {
                return $model->organisation->image_dir . '/email_attachments/{id}/{name}';
            },
            'attribute' => 'attachments',
            'deleteAttribute' => 'attachments_delete'
        ],
    ];
}
 *
 * @package common\components
 *
 * @property ActiveRecord $owner
 */
class FileMultipleBehavior extends FileBehavior
{
    const TYPE_IMAGE = 'image';
    const TYPE_FILE  = 'file';
    
    /**
     * Attribute for parsing files from html content
     * @var string
     */
    public $contentAttribute;

    /**
     * @var string image or a common file uploaded via wysiwyg
     */
    public $contentFileType = 'image';
    
    /**
     * Whether the file allowed to be deleted
     * @var callable function($attribute, $path){}
     */
    public $canDelete;

    /**
     * @var string[]
     */
    private $oldValue = [];

    /**
     * @var UploadedFile[]
     */
    private $files = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_merge([
            ActiveRecord::EVENT_AFTER_FIND => function ($e) {
                if (!$this->owner->hasAttribute($this->attribute)) {
                    $this->owner->{$this->attribute} = $this->getFiles();
                }
                $this->oldValue = $this->owner->{$this->attribute} ? : [];
            },
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => function ($e) {
                FileHelper::removeDirectory($this->getDir());
            }
        ], $this->events);
    }

    /**
     * @inheritdoc
     */
    protected function setAttributeValue($add = [], $remove = [], $update = true)
    {
        $this->files = [];
        // using array_values so we end up with json like ['a','b'], not a keyed array.
        $value = array_values(array_diff(array_merge((array) $this->oldValue, $add), $remove));
        $this->owner->{$this->attribute} = $value;
        if ($update && $this->owner->hasAttribute($this->attribute)) {
            $this->owner->update(false, [$this->attribute]);
        }
        $this->oldValue = $value;
    }

    /**
     * Deletes files (if deleteAttribute checked) and loads file attribute with UploadedFile instances for validation rules
     */
    public function beforeValidate()
    {
        if ($this->filesToDelete()) {
            $this->deleteFiles(json_decode($this->owner->getOldAttribute($this->attribute)), null, false);
        }
        $this->owner->{$this->attribute} = $this->files = $this->contentAttribute
            ? $this->processContentFiles(false)['files']
            : UploadedFile::getInstances($this->owner, $this->attribute);
    }

    /**
     * After UploadedFiles are validated we return old value to the owner attribute
     * We also add contentAttribute errors if there are errors in content parsed files
     */
    public function afterValidate()
    {
        if ($this->owner->hasAttribute($this->attribute) && $this->files) {
            $this->owner->setAttribute($this->attribute, array_map(function($v){ return $v->name; }, $this->files));
        }
        if ($this->contentAttribute) {
            $this->owner->addErrors([$this->contentAttribute => $this->owner->getErrors($this->attribute)]);
            $this->owner->clearErrors($this->attribute);
        }
        if (!$this->files) {
            $this->owner->{$this->attribute} = $this->oldValue;
        }
    }

    /**
     * Saves files according to $this->target callback/string saving path
     */
    public function afterSave()
    {
        if (!$this->files) {
            return;
        } else if ($this->contentAttribute) {
            $this->owner->updateAttributes([$this->contentAttribute => $this->processContentFiles(true)['content']]);
        } else {
            /** @var UploadedFile $file */
            foreach ($this->files as $key => $file) {
                $this->files[$key] = $this->saveFile($file->tempName, $file->name);
            }
        }
        $this->setAttributeValue(\array_map(function($v){ return basename($v); }, array_filter($this->files)));
    }

    /**
     * Saves each file
     * @param $sourcePath
     * @param $name
     * @return bool
     * @throws \yii\base\Exception
     */
    public function saveFile($sourcePath, $name)
    {
        $name = preg_replace('#[^\w-_.]#', '', $name);
        $path = str_replace(
            ['{attribute}', '{name}', '{id}'],
            [$this->attribute, $name, $this->owner->primaryKey],
            $this->targetFilePath()
        );
        $dir = dirname($path);
        FileHelper::createDirectory($dir);
        @unlink($path);
        if (!@copy($sourcePath, $path)) {
            $this->owner->addError($this->attribute,
                \Yii::t('app', 'Unable to move the {file} to target directory', ['file' => $name]));
            return false;
        }
        return $path;
    }

    /**
     * Gets saved files array as server paths or web urls
     * @param null $attribute
     * @param null $value
     * @return array
     */
    public function getFiles($attribute = null, $value = null)
    {
        if ($attribute) {
            return $this->getFileBehavior($attribute)->getFiles(null, $value);
        }
        if ($this->owner->isNewRecord && !$this->owner->{$this->attribute}) {
            return [];
        }
        if (!file_exists($this->getDir())) {
            return [];
        }
        $result = [];
        $files = (!$value && $this->owner->hasAttribute($this->attribute))
            ? $this->owner->{$this->attribute}
            : FileHelper::findFiles($this->getDir());
        foreach ((array) $files as $path) {
            if (!$path || ($value && $value != basename($path))) {
                continue;
            }
            $result[$this->getDir(). DIRECTORY_SEPARATOR . basename($path)]
                = Url::base(). str_replace(\Yii::getAlias('@frontend/web'), '', $path);
        }
        return $result;
    }

    /**
     * Deletes files by name
     * @param null|string|string[] $names  leave null to delete all
     * @param null|string $attribute
     * @param bool $update
     * @return bool
     */
    public function deleteFiles($names = null, $attribute = null, $update = true)
    {
        if ($attribute) {
            return $this->getFileBehavior($attribute)->deleteFiles($names, null, $update);
        }
        $dir = $this->getDir();
        $names = $names
            ? (array) $names
            : array_values($this->getFiles());
        foreach ($names as $name) {
            $path = $dir. DIRECTORY_SEPARATOR . basename($name);
            $this->oldValue = array_diff($this->oldValue, [basename($name)]);
            if (file_exists($path) && is_file($path)
                && (!$this->canDelete || call_user_func($this->canDelete, $this->attribute, $path))
            ) {
                @unlink($path);
            }
        }
        $this->setAttributeValue([], $names, $update);
        return true;
    }

    /**
     * Gets the correct file behavior using the $attribute hint.
     * @param $attribute
     * @return self
     */
    private function getFileBehavior($attribute)
    {
        $class = get_class($this);
        /** @var static $behavior */
        foreach ($this->owner->getBehaviors() as $behavior) {
            if (get_class($behavior) == $class && $behavior->attribute == $attribute) {
                return $behavior;
            }
        }
    }

    /**
     * This is for WYSIWYG content files
     * Extracts image/link dom objects from the content and saves as files on the server
     * Images are given as base64 encoded <img> from Summernote editor
     * Videos/Files are given as <a> with a href leading to tmp directory
     * @param bool $replace
     * @return array [string wysiwyg content, UploadedFile[]]
     */
    private function processContentFiles($replace = false)
    {
        $files = [];
        $content = \phpQuery::newDocumentHTML($this->owner->{$this->contentAttribute});
        $isImg = $this->contentFileType == static::TYPE_IMAGE;
        foreach ($els = $content->find($isImg ? 'img' : 'a') as $el) {
            $el = pq($el);
            $tempName = $this->tmpName($isImg ? null : $el->attr('href')); // image is being encoded in base64 by Summernote editor and put in a new file;  common file is preuploaded into /tmp dir, the tempname is in "href" attribute
            if (!$name = $el->attr('data-filename')) {
                continue;
            }
            if ($isImg) {// image is base64 encoded by Summernote wysiwyg editor
                if (!preg_match('#^data:image/\w+;base64,([\s\S]+)$#', str_replace(' ', '+', $el->attr('src')), $matches)) {
                    continue;
                }
                file_put_contents($tempName, base64_decode($matches[1]));
            } else if (!file_exists($tempName)) { // common file should be already preuploaded into /tmp dir
                continue;
            }
            if ($replace) { // on afterSave - save files, replace wysiwyg href/src with the real one
                $file_location = $this->saveFile($tempName, $name);
                if (dirname($tempName) === sys_get_temp_dir()) {
                    @unlink($tempName);
                }
                $url = preg_replace('/.*\/frontend\/web/', \Yii::$app->urlManager->hostInfo, $file_location);
                $el->attr($isImg ? 'src' : 'href', $url);
            } else { // on beforeValidate - generate UploadedFile instance for validation rules
                $type = FileHelper::getMimeType($tempName);
                $size = filesize($tempName);
                $files[] = new UploadedFile(compact('tempName', 'name', 'type', 'size'));
            }
        }
        return compact('content', 'files');
    }
}
