<?php

namespace app\modules\rbac\components\nodetree\actions;

use yii\base\Action;

class TreeMoveAction extends Action
{
    public function run($id)
    {
        $model = $this->controller->findModel($id);
        if(!$model->treeMove($_POST['pid'], $_POST['position'])){
            throw new \yii\web\HttpException(400, "Could not save changes");
        }
        echo json_encode($model->nodeAttributes());
    }
}