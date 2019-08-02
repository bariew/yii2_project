<?php
/**
 * DefaultController class file.
 */

namespace app\modules\test\controllers;
use app\modules\test\actions\IndexAction;
use app\modules\test\actions\ViewAction;
use app\modules\test\models\Item;
use Phpml\Classification\KNearestNeighbors;
use yii\web\Controller;

/**
 * Description.
 *
 * Usage:
 *
 */
class DefaultController extends Controller
{
    public function actionIndex()
    {
        $labels = ['a', 'a', 'a', 'b', 'b', 'b']; // classes (names) of points
        $samples = [[1, 3], [1, 4], [2, 4], [3, 1], [4, 1], [4, 2]]; // coordinates (x,y) of points

        $classifier = new KNearestNeighbors();
        $classifier->train($samples, $labels);

        echo $classifier->predict([3, 2]); // calculate what would be the name of the point with these coordinates according to other closest points
    }
}
