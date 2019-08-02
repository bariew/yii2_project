<?php
/**
 * SiteController class file.
 */

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;
use yii\web\HttpException;

/**
 * Description:
 */
class SiteController extends Controller
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
                        'actions' => ['error', 'captcha'],
                        'roles' => ['?', '@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => '\yii\captcha\CaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index\php?r=site/page&view=FileName
            'page' => array(
                'class' => '\yii\web\ViewAction',
            ),
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        /** @var HttpException $error */
        if ($error = Yii::$app->errorHandler->exception) {
            switch (@$error->statusCode) {
                case 400: $message = Yii::t('site/error', 'Bad Request');
                    break;
                case 403: $message = Yii::t('site/error', 'Access Denied');
                    break;
                case 404: $message = Yii::t('site/error', 'Page Not Found');
                    break;
                default: $message = $error->getMessage();
            }
            return $this->render('error', $message);
        }
    }
}
