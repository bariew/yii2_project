<?php
use yii\helpers\Html;
use app\modules\i18n\models\SourceMessageSearch;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\i18n\models\SourceMessageSearch $searchModel
 */
$this->title = Yii::t('modules/i18n', "Translations");
?>
<?php $form = \yii\widgets\ActiveForm::begin(['action' => ['import'], 'options' => ['class' => 'pb-4', 'enctype' => 'multipart/form-data']]); ?>
    <?= Html::fileInput('file', null, ['class' => 'd-none', 'id' => 'import-input',
        'onchange' => "$(this).parents('form').submit()",
        'accept' => '.xlsx'
    ]); ?>
    <?= Html::button('<i class="glyphicon glyphicon-download"></i>', ['class' => 'btn btn-primary float-end mx-1', 'onclick' => '$("#import-input").click()', 'title' => Yii::t('i18n', 'Import translations')]); ?>
    <?= Html::a('<i class="glyphicon glyphicon-upload"></i>', \yii\helpers\Url::current(['export']), ['class' => 'btn btn-success float-end', 'title' => Yii::t('i18n', 'Export Results')]); ?>
<?php $form::end(); ?>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns'      => [
        [
            'attribute' => 'category',
            'filter'    => \app\modules\i18n\models\SourceMessage::categoryList(),
        ],
        [
            'attribute' => 'language',
            'filter'    => \app\modules\i18n\models\Message::languageList(),
        ],
        'message',
        [
            'attribute' => 'translation',
            'format' => 'raw',
            'value' => function(SourceMessageSearch $model) {
                return $model->language
                    ? "<div contentEditable='true' style='overflow: auto' 
                        onkeydown='if (event.keyCode === 9) {
                            event.preventDefault(); event.stopPropagation();
                            $(this).parents(\"tr\").find(\".save-btn\").click();
                            $(this).parents(\"tr\").next().find(\".translate-live-input\").get(0).focus();                            
                        }'
                        class='form-control translate-live-input'
                    >{$model->translation}</div>"
                    : '';
            }
        ],
        [
            'attribute' => 'translationUpdate',
            'label'     => Yii::t('modules/i18n', 'Update'),
            'filter'    => [
                'is not null' => Yii::t('modules/i18n', 'Translated'),
                'is null'     => Yii::t('modules/i18n', 'Not translated'),
            ],
            'options' => ['width' => '120px'],
            'format' => 'raw',
            'value' => function(SourceMessageSearch $model) {
                return  \yii\helpers\Html::a("<i class='glyphicon glyphicon-plus'></i>", ['create', 'id' => $model->id], [
                    'title' => Yii::t('i18n', 'Add translation'),
                ]) . ' '
                . ($model->language
                    ? \yii\helpers\Html::a(
                        "<i class='glyphicon glyphicon-pencil'></i>",
                        ['update', 'id' => $model->id, 'language' => $model->language],
                        ['title' => Yii::t('common', 'Update')]
                    ) . ' '
                    . \yii\helpers\Html::a("<i class='glyphicon glyphicon-save'></i>", '#', [
                        'data-url'=>\yii\helpers\Url::toRoute(['fast-update', 'id' => $model->id]),
                        'class' => 'save-btn',
                        'onclick' => "
                               $(this).parents('tr').fadeOut();
                               $.post($(this).data('url'), {
                                    translation : $(this).parents('tr').find('.translate-live-input').text(),
                                    language : '".$model->language."',
                                    _csrf : '".Yii::$app->request->csrfToken."'
                               }); return false;"
                    ]) . ' '
                    . \yii\helpers\Html::a("<i class='glyphicon glyphicon-trash'></i>", '#', [
                        'data-id'=>"{$model->id}-{$model->language}",
                        'data-url'=>\yii\helpers\Url::toRoute(['delete', 'id' => $model->id, 'language' => $model->language]),
                        'onclick' => "
                               $(this).parents('tr').fadeOut();
                               $.post($(this).data('url'), {_csrf : '".Yii::$app->request->csrfToken."'}); 
                               return false;"
                    ]) : ''
                );
            },
        ],
    ],
]);
