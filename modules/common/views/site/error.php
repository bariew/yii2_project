<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var string $name
 * @var string $message
 * @var Exception $exception
 */

$this->title = $name;
?>
<div class="site-error">



    <div class="alert alert-danger">
        <?php echo nl2br(Html::encode($message)) ?>
    </div>

</div>
