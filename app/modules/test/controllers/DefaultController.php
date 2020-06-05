<?php
/**
 * DefaultController class file.
 */

namespace app\modules\test\controllers;
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

        $phpexecjs = new PhpExecJs();
        $phpexecjs->createContextFromFile(\Yii::getAlias("@app/modules/test/react/react-dom.js"));
        print_r($phpexecjs->call("React.createElement", [
            "button",
            '{ onClick: () => this.setState({ liked: true }) }',
            'Like'
        ]));
   }
}
