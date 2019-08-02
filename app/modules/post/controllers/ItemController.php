<?php
/**
 * ItemController class file.
 */

namespace app\modules\post\controllers;

use app\controllers\actions\FileUpload;
use app\controllers\actions\ImageDelete;
use app\controllers\actions\VideoUpload;
use app\modules\post\actions\CreateAction;
use app\modules\post\actions\DeleteAction;
use app\modules\post\actions\IndexAction;
use app\modules\post\actions\UpdateAction;
use app\modules\post\actions\ViewAction;
use app\modules\post\models\Category;
use app\modules\post\models\CategorySearch;
use app\modules\post\Module;
use Yii;
use app\modules\post\models\Item;
use app\modules\post\models\ItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * For managing post items.
 *
 */
class ItemController extends Controller
{
    /**
     * Gets scenario for model.
     * @return string
     */
    public function getScenario()
    {
        return Item::SCENARIO_ADMIN;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'image-delete' => [
                'class' => ImageDelete::className(),
                'getModelCallback' => function ($id) { return $this->getModel($id); }
            ],
            'video-upload' => [
                'class' => VideoUpload::className(),
                'getModelCallback' => function ($id) { return $this->getModel($id); }
            ],
            'file-upload' => [
                'class' => FileUpload::className(),
                'getModelCallback' => function ($id) { return $this->getModel($id); }
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        /**
         * @var ItemSearch $searchModel
         */
        $searchModel = $this->getModel(false, true);
        $data = [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
        ];

        return Yii::$app->request->isAjax
            ? $this->renderPartial('index-ajax', $data)
            : $this->render('index', $data);
    }

    /**
     * @inheritdoc
     */
    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->getModel($id)]);
    }
    
    /**
     * @inheritdoc
     */
    public function actionCreate()
    {
        $model = $this->getModel(null);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', Yii::t('modules/post', 'Successfully created.'));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', compact('model'));
        }
    }

    /**
     * @inheritdoc
     */
    public function actionUpdate($id)
    {
        $model = $this->getModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', Yii::t('modules/post', 'Successfully updated.'));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', compact('model'));
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if ($this->getModel($id)->delete()) {
            Yii::$app->session->addFlash('success', Yii::t('modules/post', 'Successfully deleted.'));
        } else {
            Yii::$app->session->addFlash('error', Yii::t('modules/post', 'Could not delete item.'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes file and thumbnails.
     * @param integer $id owner Item model id.
     * @param string $name file name.
     * @return \yii\web\Response
     */
    public function actionFileDelete($id, $name)
    {
        $model = $this->getModel($id);
        if ($model->deleteFile($name)) {
            Yii::$app->session->setFlash('success', Yii::t('modules/post', 'File successfully deleted'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('modules/post', 'File delete error'));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Deletes file and thumbnails.
     * @param integer $id owner Item model id.
     * @param string $name file name.
     * @return \yii\web\Response
     */
    public function actionFileRename($id, $name)
    {
        $model = $this->getModel($id);
        if (
            $name
            && ($newName = Yii::$app->request->post('newName'))
            && $newName != $name
            && $model->renameFile($name, $newName)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('modules/post', 'File successfully renamed'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('modules/post', 'File rename error'));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
    
    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer|boolean $id
     * @param boolean $search
     * @return Item|ItemSearch|Category|CategorySearch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function getModel($id = false, $search = false)
    {
        $model = $search
            ? new ItemSearch()
            : new Item();
        $model->scenario = Item::SCENARIO_ADMIN;
        if ($id && (!$model = $model->search(compact('id'))->one())) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model->scenario = Item::SCENARIO_ADMIN;
        return $model;
    }
}
