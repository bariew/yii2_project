<?php
/**
 * WidgetController class file.
 */

namespace app\modules\comment\controllers;

use yii\web\Controller;
use \app\modules\comment\models\Comment;
use \yii\db\ActiveRecord;

/**
 * This actions render content for widgets.
 * You can get it with url query or with UrlView widget.
 * This is for controlling widget content access by rbac.
 * 
 * 
 * @example 
    <?= \app\widgets\UrlView::widget([
        'url' => '/comment/widget/comment', 
        'params' => ['parent' => $model]
    ]); ?> 
    
    <?= \app\widgets\UrlView::widget([
        'url' => '/comment/widget/list', 
        'params' => ['parent' => $model]
    ]); ?> 
 *
 */
class WidgetController extends Controller
{
    public function actionComment(ActiveRecord $parent)
    {
        $model = new Comment();
        $model->parent_class = get_class($parent);
        $model->parent_id = $parent->primaryKey;
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('modules/comment', "Comment added"));
        }
        return $this->renderPartial('comment', compact('model'));
    }
    
    public function actionList(ActiveRecord $parent)
    {
        $items = Comment::find()->where([
            'parent_class' => get_class($parent),
            'parent_id' => $parent->primaryKey,
            'active' => true
        ])->orderBy(['created_at' => SORT_DESC])->all();
        return $this->renderPartial('list', [
            'dataProvider' => new \yii\data\ArrayDataProvider([
                'allModels' => $items,
            ])
        ]);
    }
}
