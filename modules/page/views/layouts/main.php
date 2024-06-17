<?php
use yii\helpers\Html;
use yii\bootstrap5\Alert;
use yii\bootstrap5\Breadcrumbs;
use app\views\AppAsset;

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
<body>
<?php $this->beginBody() ?>
    <div class="wrap">
        <?php \yii\bootstrap5\NavBar::begin( array(
            'options' => ['class' => 'navbar-light navbar-expand'],
            'brandLabel' => Yii::$app->name,
        ));
        if (Yii::$app->user->can('admin')) {
            echo \yii\bootstrap5\Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    ['label' => Yii::t('common', 'Logs'), 'items' => [
                        ['label' => Yii::t('common', 'Index'), 'url' => ['/log/item/index']],
                        ['label' => Yii::t('common', 'Errors'), 'url' => ['/log/error/index']],
                    ]],
                    ['label' => Yii::t('common', 'Users'), 'url' => ['/user/user/index']],
                ]
            ]);
        }
        if (Yii::$app->user->isGuest) {
            echo \yii\bootstrap5\Nav::widget([
                'options' => ['class' => 'navbar-nav ms-auto'],
                'items' => [
                    ['label' => 'Log In', 'url' => ['/user/default/login']],
                ]
            ]);
        } else {
            echo \yii\bootstrap5\Nav::widget([
                'options' => ['class' => 'navbar-nav ms-auto'],
                'items' => [
                    [
                        'label' => \app\modules\user\models\User::current()->email,
                        'items' => [
                            ['label' => Yii::t('common', 'Profile'), 'url' => ['/user/default/update']],
                            ['label' => Yii::t('common', 'Logout'), 'url' => ['/user/default/logout']],
                        ]
                    ],
                ]
            ]);
        }
        \yii\bootstrap5\NavBar::end();
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
    <footer class="footer">
        <div class="container">
            <p class="float-left">&copy; <?= Yii::$app->name . ' ' . date('Y') ?></p>
            <p class="float-right"><?= Yii::powered() ?></p>
        </div>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
