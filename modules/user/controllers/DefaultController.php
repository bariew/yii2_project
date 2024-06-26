<?php
/**
 * DefaultController class file.
 */

namespace app\modules\user\controllers;

use app\modules\common\components\Container;
use app\modules\common\components\Mailer;
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
     * @return string view.
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new Login();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect($this->getLoginRedirect());
        }
        return Yii::$app->request->isAjax ? $this->renderAjax('login', compact('model')) : $this->render('login', compact('model'));
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
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new Register(['status' => User::STATUS_INACTIVE]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Mailer::send(Mailer::VIEW_REGISTRATION_COMPLETE, Yii::t('mail', 'Registration complete'), ['model' => $model], $model->email);
            Yii::$app->session->addFlash('success', Yii::t('user', 'Registration confirm link has been sent to your email!'));
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
            Mailer::send(Mailer::VIEW_PASSWORD_FORGOT, Yii::t('mail', 'Password restore email'), ['model' => $model], $model->email);
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
}

