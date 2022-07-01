<?php
/**
 * MessageController class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace app\modules\i18n\controllers;

use app\modules\common\helpers\DbHelper;
use app\modules\common\widgets\Alert;
use app\modules\i18n\models\SourceMessage;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Yii;
use app\modules\i18n\models\Message;
use app\modules\i18n\models\SourceMessageSearch;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

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
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionImport()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        if ($file = UploadedFile::getInstanceByName('file')) {
            $data = [];
            $existingMessageIds = SourceMessage::find()->select('id')->column();
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->tempName);

            foreach ($reader->getSheetIterator() as $a => $sheet) { /*** @var  */
                if ($a > 1) {
                    continue;
                }
                foreach ($sheet->getRowIterator() as $k => $row) {
                    $item = array_map(function ($v) { return $v->getValue();}, $row->getCells());
                    if ($k == 1) {
                        try {
                            list($from_lang, $to_lang) = array_values(array_intersect(array_keys(Yii::$app->params['languages']), $item));
                        } catch (\Exception $e) {
                            throw new BadRequestHttpException("Incorrect Languages");
                        }
                        $keys = array_flip(array_filter($item));
                        continue;
                    }
                    if (!$item[$keys['id']]) {
                        if ($item[$keys[$to_lang]]) {
                            throw new BadRequestHttpException("Missing ID value");
                        }
                        break; // quit processing rows for there might be much more empty ones
                    }
                    if (!in_array($item[$keys['id']], $existingMessageIds)) {
                        continue;
                    }
                    $data[] = ['id' => $item[$keys['id']], 'language' => $to_lang, 'translation' => $item[$keys[$to_lang]]];
                }
            }
            $reader->close();
            Yii::$app->session->addFlash(Alert::TYPE_SUCCESS, "Successfully imported");
            foreach (array_chunk($data, 100) as $values) {
                DbHelper::insertUpdate(Message::tableName(), $values, Message::getDb());
            }
        }
        return $this->redirect('index');
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
