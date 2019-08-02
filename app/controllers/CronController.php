<?php
/**
 * Class class file.
 */

namespace app\controllers;


use yii\console\Controller;

/**
 * Description:
 */
class CronController extends Controller
{
    /**
     * @param null $dateString
     * @throws \Exception
     */
    public function actionAll($dateString = null)
    {
        ini_set('memory_limit', '-1');
        $date = new \DateTime($dateString ?: 'now', new \DateTimeZone('UTC'));
        $dateString = $dateString ?: $date->format('Y-m-d H:i:0');
        $hour = $date->format('H');
        $minute = $date->format('i');

        if ($minute % 5 == 0) { // every 5 minute
        }

        switch ($minute) {
            case 0:
                switch ($hour) {
                    case 4:
                        break;
                    case 15:
                        break;
                }
                break;
        }

        echo "DONE! \n";
    }
}
