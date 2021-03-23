<?php
/**
 * DefaultController class file.
 */

namespace app\modules\test\controllers;
use app\modules\test\ReactRenderer;
use Nacmartin\PhpExecJs\PhpExecJs;
use yii\web\Controller;

/**
 * Description.
 *
 * Usage:
 *
 */
class DefaultController extends Controller
{


    public function actionIndex()
    {
        return $this->render('index.jsx', ['model' => \Yii::$app->user->identity]);
//        $phpexecjs = new PhpExecJs();
//        $phpexecjs->createContextFromFile(\Yii::getAlias("@app/modules/test/react/react-dom.js"));
//        print_r($phpexecjs->call("React.createElement", [
//            "button",
//            '{ onClick: () => this.setState({ liked: true }) }',
//            'Like'
//        ]));
   }
}
