<?php
/**
 * OauthController class file
 */

namespace app\modules\user\controllers;

use app\modules\common\components\Container;
use app\modules\common\components\google\Token;
use app\modules\common\models\Settings;
use app\modules\common\widgets\Alert;
use Yii;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Class OauthController
 * @package app\modules\user\controllers
 */
class OauthController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['?', '@']],
                ],
            ]
        ];
    }
    /**
     * @param string $code
     * @param string $error
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionMicrosoft($code = '', $error = '')
    {
        $client = Token::authClient(
            Token::TYPE_MICROSOFT,
            Yii::$app->params['oauth']['microsoft']['clientId'],
            Yii::$app->params['oauth']['microsoft']['clientSecret'],
            Url::to(['/user/oauth/microsoft'], true)
        );
        if ($error) {
            throw new BadRequestHttpException($error);
        } else if ($code) {
            Alert::successfullySaved();
            Settings::findOrCreate(Settings::NAME_OAUTH_MICROSOFT, Token::fromProvider($client, $code));
            return $this->goBack();
        } else {
            return $this->redirect($client->getAuthorizationUrl());
        }
    }

    /**
     * @param string $code
     * @param string $error
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionGoogle($code = '', $error = '')
    {
        $client = Token::authClient(
            Token::TYPE_GOOGLE,
            Yii::$app->params['oauth']['google']['clientId'],
            Yii::$app->params['oauth']['google']['clientSecret'],
            Url::to(['/user/oauth/google'], true)
        );
        if ($error) {
            throw new BadRequestHttpException($error);
        } else if ($code) {
            Alert::successfullySaved();
            Settings::findOrCreate(Settings::NAME_OAUTH_GOOGLE, Token::fromProvider($client, $code));
            return $this->goBack();
        } else {
            return $this->redirect($client->getAuthorizationUrl());
        }
    }

    /**
     * OAuth2 login for external applications
     * @return array|string[]|Response
     * @throws ForbiddenHttpException
     */
    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        extract(Yii::$app->request->post());/** @var $redirect_uri *//** @var $client_id *//** @var $client_secret *//** @var $code *//** @var $client_id */
        if ($key = str_replace('Bearer ', '', Yii::$app->request->headers->get('Authorization'))) {
            $user = User::findIdentityByAccessToken($key);
            return ['result' => (bool) $user, 'username' => ($user ? $user->name : '')]; //zapier test
        }
        $params = Yii::$app->params['oauth']['external'];
        if (!in_array(Yii::$app->request->get('client_id', @$client_id), $params['clientId'])) {
            throw new ForbiddenHttpException();
        }
        if (Yii::$app->request->isPost) { // user successfully logged in - give them his Bearer access_token
            if (!in_array($client_secret, $params['clientSecret'])) {
                throw new ForbiddenHttpException();
            }
            return ['access_token' => User::findOne(@Container::hashids()->decode($code)[0])->key ?? null, 'expires_in' => 999999999 ,'token_type' => 'Bearer', 'scope' => 'all'];
        }
        $redirect_uri = Yii::$app->request->get('redirect_uri', @$redirect_uri).'?state='.Yii::$app->request->get('state');
        if (User::current()) {
            return $this->redirect($redirect_uri."&code=".@User::current()->id);
        }
        Yii::$app->user->setReturnUrl($redirect_uri);
        return $this->redirect(['/user/default/login', 'oauth' => 1]);
    }
}