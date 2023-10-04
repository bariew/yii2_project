<?php
/**
 * DefaultController class file.
 */

namespace app\modules\user\controllers;

use app\modules\common\components\Container;
use app\modules\user\models\Auth;
use app\modules\user\models\forms\Login;
use app\modules\user\models\forms\Register;
use yii\authclient\AuthAction;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\modules\user\models\User;
use yii\authclient\BaseOAuth;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Default controller for all users.
 * 
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
                        'roles' => ['?', '@']
                    ],
                ]
            ]
        ];
    }

    /**
     * Url for redirecting after login
     * @return null
     */
    public function getLoginRedirect()
    {
        return ["/"];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::className(),
                'successCallback' => function (BaseOAuth $client) {
                    $user = Auth::clientInstance($client)->user;
                    (new Login(['email' => $user->email]))->login(false);
                },
                'successUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/user/default/login'])
            ],
            'auth-attach' => [
                'class' => AuthAction::className(),
                'successCallback' => function (BaseOAuth $client) {
                    $model = Auth::clientInstance($client);
                    Yii::$app->session->addFlash('success', Yii::t('user/default/update', 'Successfully attached {service} account', ['service' => $model->service_id]));
                },
                'successUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/user/default/update'])
            ],
            'auth-detach' => [
                'class' => AuthAction::className(),
                'successCallback' => function (BaseOAuth $client) {
                    $model = Auth::clientInstance($client);
                    Yii::$app->session->addFlash('success', Yii::t('user/default/update', 'Successfully removed {service} account', ['service' => $model->service_id]));
                    $model->delete();
                },
                'successUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/user/default/update'])
            ],
        ];
    }

    /**
     * Renders login form.
     * @param string $view
     * @param bool $partial
     * @return string view.
     */
    public function actionLogin($view = 'login', $partial = false)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new Login();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect($this->getLoginRedirect());
        }
        if (Yii::$app->request->isAjax || $partial) {
            return $this->renderAjax($view, compact('model'));
        }
        return $this->render($view, compact('model'));
    }

    /**
     * Logs user out and redirects to homepage.
     * @return string view.
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    /**
     * Registers user.
     * @return string view.
     */
    public function actionRegister()
    {
        return;//TODO!
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new Register(['status' => User::STATUS_INACTIVE]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->trigger($model::EVENT_AFTER_COMPLETE);
            Yii::$app->session->addFlash('success', Yii::t('user', 'Password reset link is sent to your email!'));
            return $this->goHome();
        }
        return $this->render('register', compact('model'));
    }

    /**
     * For registration confirmation by email auth link.
     * @param $email
     * @param $code
     * @return string view.
     * @throws ForbiddenHttpException
     */
    public function actionEmailConfirm($email, $code)
    {
        $model = new Login(['email' => $email]);
        if ($code !== $model->resetCode(1)) {
            throw new ForbiddenHttpException(Yii::t('user', 'Your authorization link is invalid!'));
        }
        Yii::$app->session->addFlash('success', Yii::t('user', 'Email has been successfully confirmed!'));
        $model->user->status = User::STATUS_ACTIVE;
        $model->user->save(false);
        $model->login(false);
        return $this->goHome();
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionPasswordForgot()
    {
        $model = new Login();
        $model->scenario = $model::SCENARIO_PASSWORD_FORGOT;
        if ($model->load(Yii::$app->request->post()) && $model->validate() && ($user = $model->getUser())) {
            $model->trigger($model::EVENT_PASSWORD_FORGOT);
            Yii::$app->session->addFlash('success', Yii::t('user', 'Password reset link is sent to your email!'));
            return $this->goHome();
        }
        return $this->render('password_forgot', ['model' => $model]);
    }

    /**
     * @param $email
     * @param $code
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionPasswordReset($email, $code)
    {
        $model = new Login(['email' => $email]);
        $model->scenario = $model::SCENARIO_PASSWORD_RESET;
        if (!in_array($code, [$model->resetCode(), $model->resetCode(gmdate('Y-m-d', strtotime('-1day')))])) {
            throw new ForbiddenHttpException(Yii::t('user', 'Password reset code is expired!'));
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->user->save(false)) {
            Yii::$app->session->addFlash('success', Yii::t('user', 'Password has been successfully updated!'));
            $model->login(false);
            return $this->goHome();
        }
        return $this->render('password_reset', ['model' => $model]);
    }

    /**
     * @return string
     */
    public function actionUpdate()
    {
        if (!$model = User::current()) {
            return $this->goHome();
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', Yii::t('user', 'Data has been successfully updated!'));
        }
        return $this->render('update', ['model' => $model]);
    }

    /**
     * OAuth2 login for external applications
     * @return array|string[]|Response
     * @throws ForbiddenHttpException
     */
    public function actionOauth()
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
            return ['access_token' => User::findOne(decode($code))->key ?? 123, 'expires_in' => 999999999 ,'token_type' => 'Bearer', 'scope' => 'all'];
        }
        $redirect_uri = Yii::$app->request->get('redirect_uri', @$redirect_uri).'?state='.Yii::$app->request->get('state');
        if (User::current()) {
            return $this->redirect($redirect_uri."&code=".@User::current()->id);
        }
        Yii::$app->user->setReturnUrl($redirect_uri);
        return $this->redirect(['/user/default/login', 'oauth' => 1]);
    }
}