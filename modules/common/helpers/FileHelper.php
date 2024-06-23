<?php
/**
 * FileHelper class file
 */

namespace app\modules\common\helpers;

use kartik\mpdf\Pdf;
use Yii;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * Class FileHelper
 * @package app\modules\common\helpers
 */
class FileHelper
{
    const AVAILABLE_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png', 'mov', 'doc', 'docx', 'pdf', 'xls', 'xlsx', 'csv', 'ppt'];
    const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png'];
    const MAX_SIZE = 100*1024*1024;
    const MAX_FILES = 10;
    const DEFAULT_VALIDATION = [
        'file', 'checkExtensionByMimeType' => false, 'maxSize' => self::MAX_SIZE,
        'maxFiles' => self::MAX_FILES, 'extensions' => self::AVAILABLE_EXTENSIONS
    ];


    /**
     * Generates tmp file path
     * @return string
     */
    public static function tmpFile()
    {
        return tempnam(sys_get_temp_dir(), Inflector::slug(Yii::$app->name, '_'));
    }

    /**
     * @return string
     */
    public static function uploadPath()
    {
        return Yii::$app->basePath .'/web/uploads/';
    }

    /**
     * @return string
     */
    public static function tmpUploadPath()
    {
        return Yii::$app->basePath . '/web/' .  Yii::$app->params['tmpUploadDir'];
    }

    /**
     * @param string[] $extensions
     * @return string
     */
    public static function extensionsString($extensions = self::AVAILABLE_EXTENSIONS)
    {
        return '.'.implode(',.', $extensions);
    }

    /**
     * @param $file
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function isImage($file)
    {
        $mime = \yii\helpers\FileHelper::getMimeType($file);
        if($mime) {
            $parts = explode('/', $mime);
            return $parts[0] == 'image';
        }
        return false;
    }

    /**
     * Adds files to zip archive
     * @param string $sourcePath directory to zip
     * @param string $distPath result zip file path
     */
    public static function zip($sourcePath, $distPath)
    {
        $zip = new \ZipArchive();
        $zip->open($distPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $zip->addFile($filePath, basename($filePath));
            }
        }
        $zip->close();
    }

    /**
     * @param $path
     * @return string|string[]
     * @throws \yii\base\InvalidConfigException
     */
    public static function thumbnail($path)
    {
        return static::isImage($path)
            ? str_replace(Yii::getAlias('@app/web', $path), '', $path)
            : '/img/fileIcons/' . strtolower(pathinfo($path, PATHINFO_EXTENSION)) . '.png';
    }

    /**
     * @param $content
     * @param array $options
     * @return mixed
     */
    public static function pdf($content, $title = '', $options = [])
    {
        $pdf = new Pdf(ArrayHelper::merge([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_STRING,
            'content' => $content,
            'cssInline' => 'th{text-align:'.(HtmlHelper::isRtl() ? 'right':'left').'};',
            'marginTop' => 20,
            'marginBottom' => 20,
            'marginFooter' => 5,
            'methods' => [
                'SetHeader' => [
                    'L' => [
                        'content' => '',
                        'font-size' => 8,
                        'color' => '#333333',
                    ],
                    'C' => [
                        'content' => $title,
                        'font-size' => 12,
                        'color' => '#333333',
                    ],
                    'R' => [
                        'content' => Html::tag('span',
                            \Yii::t('misc', 'Generated at {date}', ['date' => \Yii::$app->formatter->asDatetime(DateHelper::now())]), [
                                'dir' => HtmlHelper::isRtl() ? 'rtl' : 'ltr'
                            ]),
                        'font-size' => 8,
                        'color' => '#333333',
                    ],
                ],
                'SetFooter' => [
                    'L' => [
                        'content' => $title,
                        'font-size' => 8,
                        'font-style' => 'B',
                        'color' => '#999999',
                    ],
                    'R' => [
                        'content' => '[ {PAGENO} ]',
                        'font-size' => 10,
                        'font-style' => 'B',
                        'font-family' => 'serif',
                        'color' => '#333333',
                    ],
                    'line' => true,
                ]
            ],
            'options' => [
                'title' => $title,
            ],
        ], $options));
        return $pdf->render();
    }
}