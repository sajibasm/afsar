<?php

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var \Exception $exception */

$this->title = $name;
$code = $exception->statusCode ?? 500;

// Optional: Define custom titles/icons/messages per error code
switch ($code) {
    case 403:
        $icon = 'fas fa-ban';
        $customTitle = 'Access Denied';
        $customMessage = 'You are not authorized to access this page.';
        $color = 'text-warning';
        break;
    case 404:
        $icon = 'fas fa-search-minus';
        $customTitle = 'Page Not Found';
        $customMessage = 'Sorry, the page you are looking for does not exist.';
        $color = 'text-info';
        break;
    default:
        $icon = 'fas fa-exclamation-triangle';
        $customTitle = $name;
        $customMessage = $message;
        $color = 'text-danger';
}
?>

<style>
    .error-page {
        padding: 40px 20px;
    }
    .error-page .headline {
        font-size: 60px;
        margin-bottom: 20px;
    }
</style>

<section class="content">
    <div class="error-page text-center">
        <h2 class="headline <?= $color ?>"><?= Html::encode($code) ?></h2>

        <div class="error-content mt-4">
            <h3><i class="<?= $icon ?> <?= $color ?>"></i> <?= Html::encode($customTitle) ?></h3>

            <p class="lead text-muted mt-3">
                <?= nl2br(Html::encode($customMessage)) ?>
            </p>

            <p class="mb-4">
                If you think this is a server error, please contact support.
            </p>

            <div class="d-flex justify-content-center gap-2">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Go Back', Yii::$app->request->referrer ?: Yii::$app->homeUrl, [
                    'class' => 'btn btn-outline-secondary mr-2'
                ]) ?>

                <?= Html::a('<i class="fas fa-tachometer-alt"></i> Return to Dashboard', Yii::$app->homeUrl, [
                    'class' => 'btn btn-primary'
                ]) ?>
            </div>
        </div>
    </div>
</section>