<?php
/**
 * ContactForm class file.
 */

namespace app\modules\page\widgets;

use app\modules\user\models\User;
use Yii;
use yii\base\Widget;

/**
 * Description:
 */
class ContactForm extends Widget
{
    public function run()
    {
        $model = new \app\modules\page\models\ContactForm(['email' => User::current()->email]);
        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            Yii::$app->session->addFlash('success', Yii::t('alert', 'contact_success_title'));
        }
        return $this->render('contact', ['model' => $model]);
    }
}