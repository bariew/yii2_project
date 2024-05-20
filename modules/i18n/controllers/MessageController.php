<?php
/**
 * MessageController class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace app\modules\i18n\controllers;

use app\modules\common\helpers\DbHelper;
use app\modules\common\helpers\FileHelper;
use app\modules\common\widgets\Alert;
use app\modules\i18n\models\SourceMessage;
use app\modules\user\models\User;
use Box\Spout\Common\Entity\Cell;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Style;
use Yii;
use app\modules\i18n\models\Message;
use app\modules\i18n\models\SourceMessageSearch;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;


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
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionImport()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        if ($file = UploadedFile::getInstanceByName('file')) {
            $sources = [];
            $en = [];
            $data = [];
            SourceMessage::deleteAll();
            Message::deleteAll();
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
                            list($from_lang, $to_lang) = array_values(array_intersect($item, array_keys(Yii::$app->params['languages'])));
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
                    $sources[] = ['id' => $item[$keys['id']], 'message' => $item[$keys['message']], 'category' => $item[$keys['category']]];
                    $en[] = ['id' => $item[$keys['id']], 'language' => $from_lang, 'translation' => $item[$keys[$from_lang]]];
                    $data[] = ['id' => $item[$keys['id']], 'language' => $to_lang, 'translation' => $item[$keys[$to_lang]]];
                }
            }
            $reader->close();
            Yii::$app->session->addFlash(Alert::TYPE_SUCCESS, "Successfully imported");
            DbHelper::insertUpdate(SourceMessage::tableName(), $sources, SourceMessage::getDb());
            DbHelper::insertUpdate(Message::tableName(), $en, Message::getDb());
            DbHelper::insertUpdate(Message::tableName(), $data, Message::getDb());
        }
        return $this->redirect('index');
    }

    /**
     * @return \yii\console\Response|Response
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionExport()
    {
        /** @var ActiveQuery $query */
        $model = new SourceMessageSearch;
        $model->language = $model->language ? : 'en';
        $query = $model->search(Yii::$app->request->getQueryParams())->query;
        $data = $query->select(['t.id', 't.category', 't.message', 'en' => 't.id', $model->language => new Expression('ANY_VALUE(messages.translation)')])->asArray()->all();
        $writer = WriterEntityFactory::createXLSXWriter();
        $file = FileHelper::tmpFile();
        $writer->openToFile($file);
        $writer->addRow(new Row(array_map(function ($v) { return new Cell($v); }, array_diff(array_keys($data[0]), ['messages'])), new Style()));
        foreach ($data as $row) {
            $row['en'] = @$row['messages']['en']['translation'];
            unset($row['messages']);
            $writer->addRow(new Row(array_map(function ($v) { return new Cell($v); }, $row), new Style()));
        }
        $writer->close() ;
        return Yii::$app->response->sendContentAsFile(file_get_contents($file), 'translations.xlsx', ['mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
