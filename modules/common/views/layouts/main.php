<?php
use yii\helpers\Html;
use yii\bootstrap5\Alert;
use yii\bootstrap5\Breadcrumbs;
use app\modules\common\views\AppAsset;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
\app\modules\common\views\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?= Html::csrfMetaTags() ?>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= strip_tags((string) $this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody(); ?>

<div class="wrap">
    <?php \yii\bootstrap5\NavBar::begin( array(
        'options' => ['class' => 'navbar-dark navbar-expand bg-dark'],
        'brandLabel' => '<i class="glyphicon glyphicon-home"></i> H O M E',
    ));
    if (Yii::$app->user->can(\app\modules\rbac\models\AuthItem::ROLE_ROOT)) {
        echo \yii\bootstrap5\Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                ['label' => Yii::t('common', 'Logs'), 'items' => [
                    ['label' => Yii::t('common', 'Index'), 'url' => ['/log/item/index']],
                    ['label' => Yii::t('common', 'Errors'), 'url' => ['/log/error/index']],
                ]],
                ['label' => Yii::t('common', 'Pages'), 'url' => ['/page/item/index']],
                ['label' => Yii::t('common', 'Translations'), 'url' => ['/i18n/message/index']],
                ['label' => Yii::t('common', 'Permissions'), 'url' => ['/rbac/auth-item/index']],
                ['label' => Yii::t('common', 'Posts'), 'url' => ['/post/item/index']],
                ['label' => Yii::t('common', 'Users'), 'url' => ['/user/user/index']],
                ['label' => Yii::t('common', 'Nalog'), 'items' => [
                    ['label' => 'Transactions', 'url' => ['/nalog/transaction/index']],
                    ['label' => 'Sources', 'url' => ['/nalog/source/index']],
                ]],
            ]
        ]);
    }
    echo \yii\bootstrap5\Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => Yii::$app->user->isGuest
            ? [['label' => Yii::t('common', 'Login'), 'url' => ['/user/default/login'], 'linkOptions' => ['data-bs-toggle' => 'ajax-modal']],]
            : [['label' => Yii::t('common', 'Logout'), 'url' => ['/user/default/logout']],]
    ]);
    \yii\bootstrap5\NavBar::end();
    ?>
    <div class="container">
        <h1 class=""><?= $this->title; ?></h1>
        <hr>
        <?= '';//Breadcrumbs::widget(['links' => $this->params['breadcrumbs'] ??  []]) ?>
        <?php foreach(Yii::$app->session->getAllFlashes() as $key=>$message): ?>
            <?= Alert::widget([
                'options' => ['class' => 'alert-'.($key == 'error' ? 'danger' : $key)],
                'body' => implode('<br />', (array) $message),
            ]); ?>
        <?php endforeach; ?>
        <?= $content ?>
    </div>
</div>

<?= \app\modules\common\widgets\Modal::widget(); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
