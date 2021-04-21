<?php

namespace app\modules\nalog\controllers;

use app\modules\common\helpers\DateHelper;
use app\modules\common\widgets\Alert;
use app\modules\nalog\components\Cbr;
use app\modules\nalog\models\CurrencyHistory;
use app\modules\nalog\models\Source;
use app\modules\user\models\User;
use Yii;
use app\modules\nalog\models\Transaction;
use app\modules\nalog\models\TransactionSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TransactionController implements the CRUD actions for Transaction model.
 */
class TransactionController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']]
                ]
            ]
        ];
    }

    /**
     * Lists all Transaction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransactionSearch(['user_id' => User::current()->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Transaction model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return Yii::$app->request->isAjax
            ? $this->renderAjax('view', ['model' => $model,])
            : $this->render('view', ['model' => $model,]);
    }

    /**
     * Creates a new Transaction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transaction(['user_id' => User::current()->id, 'date' => DateHelper::now()]);
        if ($prev = Transaction::last()) {
            $model->load($prev->attributes, '');
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Yii::$app->request->referrer ? : ['index']);
        }

        return Yii::$app->request->isAjax
            ? $this->renderAjax('create', ['model' => $model,])
            : $this->render('create', ['model' => $model,]);
    }

    /**
     * @return string
     */
    public function actionCbr()
    {
        $model = new CurrencyHistory();
        return $this->render('cbr', ['model' => $model, 'data' => $model->search(Yii::$app->request->get())->asArray()->all()]);
    }

    /**
     * Updates an existing Transaction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Yii::$app->request->referrer ? : ['index']);
        }

        return Yii::$app->request->isAjax
            ? $this->renderAjax('update', ['model' => $model,])
            : $this->render('update', ['model' => $model,]);
    }

    /**
     * Deletes an existing Transaction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Yii::$app->request->referrer ? : ['index']);
    }

    /**
     * @param $date
     * @param int $amount
     * @param string $currency
     * @return float|int|mixed|string
     */
    public function actionConvert($date, $amount, $currency)
    {
        //http://www.cbr.ru/development/sxml/
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$date) {
            return "Date is required";
        }
        $currency = $currency ? : 'RUB';
        $data = Cbr::instance()->GetCursOnDate(date("Y-m-d", strtotime($date)));
        foreach ($data['ValuteData']['ValuteCursOnDate'] as $item) {
            if ($item['VchCode'] == $currency) {
                return round(str_replace(',', '.', $item['Vcurs']) * $amount, 2);
            }
        }
        return $amount;
    }



    /**
     * Finds the Transaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transaction::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('models/nalog', 'The requested page does not exist.'));
    }
}
