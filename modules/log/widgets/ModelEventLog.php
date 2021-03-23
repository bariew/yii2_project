<?php
/**
 * Created by PhpStorm.
 * User: pt
 * Date: 28.10.15
 * Time: 19:28
 */

namespace app\modules\log\widgets;


use app\modules\log\models\LogSearch;
use yii\base\Widget;

class ModelEventLog extends Widget
{
    /** @var  \yii\db\ActiveRecord */
    public $model;
    public $viewFile = 'event_log';
    public function run()
    {
        $searchModel = new LogSearch();
        $dataProvider = $searchModel->search([
            'model_name' => get_class($this->model),
            'model_id' => $this->model->primaryKey
        ], '');
        $dataProvider->sort = false;
        $dataProvider->query->orderBy(['created_at' => SORT_DESC]);
        return $this->render($this->viewFile, compact('dataProvider'));
    }
}