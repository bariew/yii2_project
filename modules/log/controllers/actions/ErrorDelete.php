<?php
namespace app\modules\log\controllers\actions;
use app\modules\log\models\Error;
use yii\base\Action;
use Yii;
/**
 * Created by PhpStorm.
 * User: pt
 * Date: 18.01.16
 * Time: 20:04
 */

class ErrorDelete extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        Error::findOne($id)->delete();
        return $this->controller->redirect(['index']);
    }
}