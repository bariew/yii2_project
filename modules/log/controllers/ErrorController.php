<?php
/**
 * ErrorController class file
 */
 
namespace app\modules\log\controllers;

use app\modules\user\models\User;
use Yii;
use app\modules\log\models\Error;
use app\modules\log\models\ErrorSearch;
use yii\db\ActiveQuery;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ErrorController implements the CRUD actions for Error model.
 */
class ErrorController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        $searchModel = new ErrorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => Error::findOne($id),
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function actionDelete($id)
    {
        Error::findOne($id)->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }
    
    /**
     * @inheritdoc
     */
    public function actionDeleteAll()
    {
        $searchModel = new ErrorSearch();
        $query = $searchModel->search(Yii::$app->request->get())->query; /** @var ActiveQuery $query */
        Error::deleteAll($query->where);
        return $this->redirect(Yii::$app->request->referrer);
    }
    
    /**
     * Finds the Error model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Error the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Error::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
