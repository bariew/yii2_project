<?php
/**
 * TagsInput class file
 */

namespace app\modules\common\widgets\apexcharts;

use Yii;
use yii\helpers\Html;
use yii\web\AssetBundle;

/**
 * Class TagsInput
 * @package app\modules\common\widgets
 *
 * Renders Input with adding multiple tags
 */
class ApexCharts extends AssetBundle
{
    public $sourcePath = __DIR__.'/assets';

    public $css = ['apexcharts.css',];
    public $js = ['apexcharts.js',];
    /**
     * @inheritDoc
     */
    public static function widget($data, $config = [])
    {
        if (!$data) {
            return '';
        }
        $id = $config['id'] ?? 'apexcharts';
        static::register(\Yii::$app->view);
        $columns = array_diff(array_keys(reset($data)), ['date']);
        $categories = json_encode(array_keys($data));
        $series = json_encode(array_map(function ($column) use ($data) { return [
            'name' => $column, 'data' => array_column($data, $column)
        ]; }, $columns));
        Yii::$app->view->registerJs(<<<JS
        var options = {
          series: {$series},
          chart: {
              height: 350,
              type: 'line',
              dropShadow: {
                enabled: true,
                color: '#000',
                top: 18,
                left: 7,
                blur: 10,
                opacity: 0.2
              },
              toolbar: {
                show: false
              }
            },
            colors: ['#77B6EA', '#545454', '#A52A2A', '#000FF', '#D2691E', '#5F9EA0', '#006400', '#00008B', '#FF8C00', '#FFD700', '#90EE90'],
            title: { text: 'Average Price', align: 'left' },
            xaxis: { categories: {$categories}, title: {text: 'Date'} },
            yaxis: { title: { text: 'RUB' } },
            legend: {
              position: 'top',
              horizontalAlign: 'right',
              floating: true,
              offsetY: -25,
              offsetX: -5
            }
        };
        var chart = new ApexCharts(document.querySelector("#{$id}"), options);
        chart.render();
JS
        );
        return Html::tag('div', '', ['id' => $id]);
    }
}