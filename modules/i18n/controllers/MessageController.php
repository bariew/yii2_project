<?php
/**
 * MessageController class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace app\modules\i18n\controllers;

use app\modules\i18n\models\SourceMessage;
use Yii;
use app\modules\i18n\models\Message;
use app\modules\i18n\models\SourceMessageSearch;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Manages site translation from admin area.
 *
 * Usage:
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 */
class MessageController extends Controller
{
    /**
     * @inheritDoc
     */
    public function actions()
    {
        return [
            'image-upload' => [
                'class' => \vova07\imperavi\actions\UploadFileAction::class,
                'url' => '/uploads/i18n/', // Directory URL address, where files are stored.
                'path' => '@app/web/uploads/i18n', // Or absolute path to directory where files are stored.
            ],
        ];
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SourceMessageSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        return $this->render('index', compact('dataProvider', 'searchModel'));
    }

    /**
     * Fast translation update.
     *
     * @param int $id
     * @return string
     */
    public function actionFastUpdate($id)
    {
        if (!$model = Message::findOne(['id' => $id, 'language' => Yii::$app->request->post('language')])) {
            throw new NotFoundHttpException();
        }
        $model->translation = Yii::$app->request->post('translation');
        $model->save();
    }

    /**
     * @param $id
     * @return string
     */
    public function actionCreate($id)
    {
        $model = new Message(['id' => $id]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', Yii::t('modules/i18n', 'Successfully saved.'));
            return $this->goBack();
        } else {
            Yii::$app->user->setReturnUrl(Yii::$app->request->referrer);
        }
        if ($model->getFirstError('language')) { // not unique
            return $this->actionUpdate($id, Yii::$app->request->post($model->formName())['language']);
        }
        return $this->render('create', compact('model'));
    }


    /**
     * @param $id
     * @param $language
     * @return string
     */
    public function actionUpdate($id, $language)
    {
        if (!$model = Message::findOne(['id' => $id, 'language' => $language])) {
            throw new NotFoundHttpException();
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', Yii::t('modules/i18n', 'Successfully saved.'));
            return $this->goBack();
        } else {
            Yii::$app->user->setReturnUrl(Yii::$app->request->referrer);
        }
        return $this->render('update', compact('model'));
    }

    /**
     * Deletes all source and translation messages.
     * @param $id
     * @param $language
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id, $language)
    {
        Message::findOne(['id' => $id, 'language' => $language])->delete();
    }

    /**
     * @param $id
     * @return array[]
     */
    public function actionSync($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return array_map(function (SourceMessage $v) {
            return [
                'source' => $v->getAttributes(['category', 'message']),
                'messages' => array_map(function (Message $w) {
                    return $w->getAttributes(['language', 'translation']);
                }, $v->messages)
            ];
        }, SourceMessage::find()->andWhere(['>', 'id', $id])->with('messages')->all());
    }

    /**
     * @return \yii\console\Response|Response
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionExport()
    {
        /** @var ActiveQuery $query */
        $model = new SourceMessageSearch;
        $model->language = $model->language ? : 'en';
        $query = $model->search(Yii::$app->request->getQueryParams())->query;
        $data = $query->select(['t.id', 't.category', 't.message', 'en' => 't.id', $model->language => new Expression('ANY_VALUE(messages.translation)')])->asArray()->all();
        $csv = $data ? [implode("\t", array_diff(array_keys($data[0]), ['messages']))] : array();
        foreach ($data as $row) {
            $row['en'] = @$row['messages']['en']['translation'];
            unset($row['messages']);
            $csv[] = implode("\t", $row);
        }
        $csv = implode("\n", $csv);
        $csv = chr(255) . chr(254) . mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');

        return Yii::$app->response->sendContentAsFile($csv, 'translations.csv', ['mimeType' => 'text/csv']);
    }
}
