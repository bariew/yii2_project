<?php
namespace app\modules\log\controllers\actions;
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

class ErrorIndex extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $searchModel = new ErrorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->controller->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}