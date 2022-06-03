<?php

namespace app\modules\product\controllers;

use app\modules\product\models\CategorySearch;
use app\modules\product\models\Item;
use app\modules\product\models\ItemSearch;
use Yii;
use app\modules\product\models\Category;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    public $layout = 'menu';

    /**
     * @inheritdoc
     */
    public function actionCreate($id)
    {
        $model = $this->findModel(null);
        $root = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->appendTo($root)) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithChildren();

        return $this->goBack();
    }

    /**
     * @inheritdoc
     */
    public function actionTreeMove($id)
    {
        $child = $this->findModel($id);
        $parent = $this->findModel(\Yii::$app->request->post('pid'));
        $position = \Yii::$app->request->post('position');
        if ((!$leaves = $parent->leaves()->all()) || ($position == 0)) {
            $child->prependTo($parent);
        } else if(count($leaves) <= $position) {
            $child->insertAfter(end($leaves));
        } else {
            $child->insertAfter($leaves[$position-1]);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function actionTreeUpdate($id)
    {
        $model = $this->findModel($id);
        $attributes = [
            'name' => \Yii::$app->request->post('attributes')['title']
        ];
        if ($model->load($attributes, '') && $model->save()) {
            return true;
        }
        throw new \yii\web\BadRequestHttpException();
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer|boolean $id
     * @param boolean $search
     * @return Category|CategorySearch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id = false, $search = false)
    {
        $model = $search
            ? new CategorySearch()
            : new Category();
        $model->scenario = Item::SCENARIO_ADMIN;
        if ($id && (!$model = $model->search(compact('id'))->one())) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model->scenario = Category::SCENARIO_ADMIN;
        return $model;
    }
}
