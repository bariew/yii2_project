<?php
use yii\helpers\Html;
use yii\bootstrap4\Alert;
use yii\bootstrap4\Breadcrumbs;
use app\modules\common\views\AppAsset;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?= Html::csrfMetaTags() ?>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<!--<body class="bg-white" style="background-image: url(/img/bg_brick.jpg);background-size: 600px;">-->
<body class="bg-black">
<?php $this->beginBody() ?>
<style>
    body {
        background-color: #fff;
        transition: background .5s ease-out;
    }
    body:hover {
        background-color: #000;
    }
</style>
<div class="wrap">
    <?php \yii\bootstrap4\NavBar::begin( array(
        'options' => ['class' => 'navbar-light navbar-expand '],
        'brandLabel' => '<i class="glyphicon glyphicon-home h1"></i>',
        'brandOptions' => ['class' => 'text-dark ']
    ));
    if (Yii::$app->user->can(\app\modules\rbac\models\AuthItem::ROLE_ROOT)) {
        echo \yii\bootstrap4\Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                ['label' => Yii::t('home', 'Logs'), 'items' => [
                    ['label' => Yii::t('home', 'Index'), 'url' => ['/log/item/index']],
                    ['label' => Yii::t('home', 'Errors'), 'url' => ['/log/error/index']],
                ]],
                ['label' => Yii::t('home', 'Pages'), 'url' => ['/page/item/index']],
                ['label' => Yii::t('home', 'Translations'), 'url' => ['/i18n/message/index']],
                ['label' => Yii::t('home', 'Permissions'), 'url' => ['/rbac/auth-item/index']],
                ['label' => Yii::t('home', 'Posts'), 'url' => ['/post/item/index']],
                ['label' => Yii::t('home', 'Users'), 'url' => ['/user/user/index']],
                ['label' => Yii::t('home', 'Nalog'), 'items' => [
                    ['label' => 'Transactions', 'url' => ['/nalog/transaction/index']],
                    ['label' => 'Sources', 'url' => ['/nalog/source/index']],
                ]],
            ]
        ]);
    }
    if (!Yii::$app->user->isGuest) {
        echo \yii\bootstrap4\Nav::widget([
            'options' => ['class' => 'navbar-nav ml-auto'],
            'items' => [
                ['label' => Yii::t('home', 'Logout'), 'url' => ['/user/default/logout']],
            ]
        ]);
    }
    \yii\bootstrap4\NavBar::end();
    ?>
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
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
