<?php
/**
 * ItemController class file.
 */

namespace app\modules\page\controllers;

use Yii;
use app\modules\page\models\Item;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Manages page models. Also has menu tree.
 */
class ItemController extends Controller
{
    /**
     * Renders JSTree menu
     * @return string|void
     */
    public function getMenu()
    {
        return Item::findOne(['pid'=>0])->menuWidget();
    }

    /**
     * @inheritdoc
     */
    public function actions() 
    {
        return [
            'tree-move'      => \bariew\nodeTree\actions\TreeMoveAction::className(),
            'tree-update'    => \bariew\nodeTree\actions\TreeUpdateAction::className(),
        ];
    }

    /**
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Item();
        $model->pid = $id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('modules/page', 'Success'));
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', ['model' => $model,]);
        }
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('modules/page', 'Success'));
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', ['model' => $model,]);
        }
    }

    /**
     * Deletes an existing Item model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->session->setFlash('success', Yii::t('modules/page', 'Success'));
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id = null)
    {
        if ($id === null) {
            return new Item();
        }
        if (($model = Item::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('modules/page', 'The requested page does not exist.'));
        }
    }
}
