<?php
/**
 * DefaultController class file.
 */

namespace app\modules\page\controllers;

use app\modules\page\models\Item;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Renders common page.
 *
 */
class DefaultController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view', 'index'],
                        'roles' => ['?', '@']
                    ]
                ]
            ]
        ];
    }
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Renders common page view.
     * @param string $url relative url from route.
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionView($url = '/')
    {
        /**
         * @var Item $model
         */
        if (!$model = Item::getCurrentPage($url)) {
            throw new \yii\web\HttpException(404, \Yii::t('modules/page', "Page not found"));
        }

        if ($model->layout) {
            $this->layout = $model->layout;
        }

        return $this->render('view', compact('model'));
    }
}
