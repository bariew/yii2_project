<?php
/**
 * ArrayHelper class file
 */

namespace app\modules\common\helpers;

use League\Csv\Reader;
use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use function PHPUnit\TestFixture\func;

/**
 * Class ArrayHelper
 * @package app\modules\common\helpers
 */
class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * @param $array
     * @param $column
     * @return float|int
     */
    public static function sum($array, $column)
    {
        return array_sum(\yii\helpers\ArrayHelper::getColumn($array, $column));
    }

    /**
     * @param $array
     * @return mixed|null
     */
    public static function min(...$array)
    {
        asort($array);
        return array_filter($array) ? array_values(array_filter($array))[0] : null;
    }

    /**
     * Indexing and grouping array by the same column element.
     * @param $array
     * @param $key
     * @param null $value
     * @return array
     */
    public static function group($array, $key, $value = null)
    {
        $result = [];
        foreach ($array as $item) {
            $result[$item[$key]][] = isset($value) ? $item[$value] : $item;
        }
        return $result;
    }

    /**
     * @return array
     */
    public static function booleanList()
    {
        return [
            true => Yii::t('common', 'Yes'),
            false => Yii::t('common', 'No')
        ];
    }

    /**
     * array_map with remaining keys
     * @param $array
     * @param $callback
     * @return mixed
     */
    public static function mapAssoc($array, $callback)
    {
        foreach ($array as $k => $v) {
            $array[$k] = $callback($k, $v);
        }
        return $array;
    }

    /**
     * array_diff_assoc with subarrays
     * @param mixed ...$arrays
     * @return array
     */
    public static function diffAssoc(...$arrays)
    {
        $result = func_get_args();
        foreach ($result as $key => $item) {
            $result[$key] = array_map('serialize', $item);
        }
        return array_map('unserialize', array_diff_assoc(...$result));
    }

    /**
     * @param $array
     * @param $from
     * @param $to
     * @param null $group
     * @return array
     */
    public static function map($array, $from, $to, $group = null)
    {
        foreach ([&$from, &$to] as &$item) {
            if (is_callable($item) || !preg_match('/^(.+):(.+)$/', $item, $matches)) {
                continue;
            }
            $item = function ($v) use ($matches) {
                return isset($v[$matches[1]]) ? Yii::$app->formatter->format($v[$matches[1]], $matches[2]) : null;
            };
        }
        return \yii\helpers\ArrayHelper::map($array, $from, $to, $group);
    }

    /**
     * @param $array
     * @param $key
     * @return array
     */
    public static function keyOrFull($array, $keys)
    {
        $result = $array;
        foreach ((array) $keys as $key) {
            if (!isset($result[$key])) {
                return $array;
            }
            $result = $result[$key];
        }
        return $result;
    }


    /**
     * @param $data
     * @return array|false|string|string[]|null
     */
    public static function toCsv($data)
    {
        $csv = array(); // defines preferred separator
        foreach ($data as $row)  {
            $csv[] = implode("\t", $row);
        }
//        $csv = preg_replace("/(\d{2})\.(\d{2})\.(\d{2})/", "$1/$2/$3", $csv);
        return mb_convert_encoding(implode("\n", $csv), 'UTF-16LE', 'UTF-8');
    }

    /**
     * @param $path
     * @return array
     */
    public static function fromCsv($path)
    {
        $content = mb_convert_encoding(file_get_contents($path), 'UTF-8', 'UTF-16LE');
        $result = [];
        foreach (explode("\n", $content) as $row) {
            $result[] = explode("\t", $row);
        }
        return $result;
    }

    /**
     * @param $data
     * @return false|string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function toExcel($data)
    {
        $path = FileHelper::tmpUploadPath() . '/' . microtime(false).'.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data);
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);
        $content = file_get_contents($path);
        unlink($path);
        return $content;
    }

    /**
     * @param $path
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public static function fromExcel($path)
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        return $spreadsheet->getSheet(0)->toArray();
    }

    /**
     * Array diff for array values
     * @param $a
     * @param $b
     * @return bool
     */
    public static function diff($a, $b)
    {
        foreach (array_values($a) as $k => $v) {
            if (array_diff((array) $v, $b)) {
                return true;
            }
        }
        return false;
    }
}