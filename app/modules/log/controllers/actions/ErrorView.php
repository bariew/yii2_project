<?php
namespace app\modules\log\controllers\actions;
use app\modules\log\models\Error;
use app\modules\log\models\ErrorSearch;
use yii\base\Action;
use yii\base\InlineAction;
use Yii;
/**
 * Created by PhpStorm.
 * User: pt
 * Date: 18.01.16
 * Time: 20:04
 */

class ErrorView extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        return $this->controller->render('view', [
            'model' => Error::findOne($id),
        ]);
    }
}