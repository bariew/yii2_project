<?php
/**
 * RubixController class file
 */


namespace app\modules\nalog\controllers;


use app\modules\nalog\components\Yfinance;
use app\modules\nalog\models\CurrencyHistory;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\CrossValidation\HoldOut;
use Rubix\ML\CrossValidation\Metrics\Accuracy;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * Class RubixController
 * @package app\modules\nalog\controllers
 */
class RubixController extends Controller
{
    public function actionTmp()
    {
        Yfinance::call();
        exit;
        $target = 'eur';
        $items = CurrencyHistory::find()->orderBy('date')->andWhere(['between', 'date', '2019-06-30', '2020-06-30'])->asArray()->all();
        $data = [];
        $labels = [];
        $diffs = [];
        foreach ($items as $i => $item) {
            if (!isset($items[$i-1])) {
                continue;
            }
            foreach ($item as $attribute => $value) {
                if ($attribute == 'date') continue;
                $value = $value ?? $items[$i-1][$attribute]; // if null
                $prev = $items[$i-1][$attribute] ?? $value; // if null
                $next = $items[$i+1][$attribute] ?? $value;
                $data[$i-1][] = $value-$prev;
                $diffs[$attribute]['minus'][] = $value-$prev;
                $diffs[$attribute]['plus'][] = $next-$value;
            }
            $next = $items[$i+1][$target] ?? $item[$target];
            $labels[$i-1] = $next-$item[$target];
        }

        foreach ($diffs as $attribute => $diff) {
            echo "{$attribute}: ";
            foreach ($diffs as $attribute2 => $diff2) {
                $correlation =  static::correlation($diff['minus'], $diff2['plus']);
                if ($correlation < 0.5) {
                    continue;
                }
                echo "{$attribute2} " . $correlation . ', ';
            }
            echo '<br>';
        }


//        echo (new HoldOut(0.2))->test(
//            new KNearestNeighbors(count($item)-1),
//            new Labeled($data, array_map(function($v) { return  [-1 => 'Minus', 0 => 'Null', 1 => 'Plus'][$v<=>0]; }, $labels)),
//            new Accuracy()
//        );
    }

    public function actionCorrelation()
    {
        $columns = ['usd', 'gbp'];
        $items = CurrencyHistory::find()->orderBy('date')->andWhere(['>', 'date', '2008-06-30'])
            ->select($columns)->asArray()->all();
        $first = ArrayHelper::getColumn($items, $columns[0]);
        $second = ArrayHelper::getColumn($items, $columns[1]);
        array_pop($first); //
        array_shift($second);
        echo static::correlation($first, $second);
    }

    public function actionEstimateAccuracy()
    {
        $estimator = new KNearestNeighbors(3);
        $dataset = new Labeled(
            [
                [4, 3, 44.2],
                [4, 3, 44.2],
                [4, 3, 44.2],
                [2, 2, 16.7],
                [2, 4, 19.5],
                [3, 3, 55.0],
                [3, 4, 50.5],
                [1, 5, 24.7],
                [4, 4, 62.0],
                [3, 2, 31.1],
            ],
            ['married', 'divorced', 'married', 'divorced', 'married', 'divorced', 'married', 'divorced', 'married', 'divorced']
        );

        $score = (new HoldOut(0.2))->test($estimator, $dataset, new Accuracy());

        echo $score;
    }

    public function actionSimpleTrain()
    {
        $estimator = new KNearestNeighbors(3);
        $estimator->train(new Labeled(
            [
                [3, 4, 50.5],
                [1, 5, 24.7],
                [4, 4, 62.0],
                [3, 2, 31.1],
            ],
            ['married', 'divorced', 'married', 'divorced']
        ));
        var_dump($estimator->predict(new Unlabeled([
            [4, 3, 44.2],
            [2, 2, 16.7],
            [2, 4, 19.5],
            [3, 3, 55.0],
            [4, 3, 44.2],
            [2, 2, 16.7],
            [2, 4, 19.5],
            [3, 3, 55.0],
        ])));
    }

    public static function correlation($x,$y)
    {
        if(count($x)!==count($y)){return -1;}
        $x=array_values($x);
        $y=array_values($y);
        $xs=array_sum($x)/count($x);
        $ys=array_sum($y)/count($y);
        $a=0;$bx=0;$by=0;
        for($i=0;$i<count($x);$i++){
            $xr=$x[$i]-$xs;
            $yr=$y[$i]-$ys;
            $a+=$xr*$yr;
            $bx+=pow($xr,2);
            $by+=pow($yr,2);
        }
        $b = sqrt($bx*$by);
        if($b==0) return 0;
        return $a/$b;
    }
}