  <?php
use bedezign\yii2\audit\web\JSLoggingAsset;
use dmstr\helpers\AdminLteHelper;
use yii\helpers\Html;
  use yii\helpers\Url;
  use yii\web\JqueryAsset;

  /* @var $this \yii\web\View */
/* @var $content string */


if (Yii::$app->controller->action->id === 'login') {
/**
 * Do not use this code in your template. Remove it.
 * Instead, use the code  $this->layout = '//main-login'; in your controller.
 */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    dmstr\web\AdminLteAsset::register($this);
    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    $this->registerJsFile('@web/lib/js/alert/confirm-buttons.js', ['depends' => [JqueryAsset::class]]);
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            .swal2-custom-popup {
                font-size: 14px !important;
                padding: 1rem !important;
            }

            .swal2-custom-title {
                font-size: 16px !important;
                font-weight: 400 !important;
            }

            .swal2-custom-text {
                font-size: 14px !important;
            }

                 /* Bigger confirmation popup */
             .swal2-confirm-popup {
                 font-size: 14px !important;
             }

            /* Bigger and bold title */
            .swal2-confirm-title {
                font-size: 18px !important;
                font-weight: 600 !important;
            }

            /* Bigger body text */
            .swal2-confirm-text {
                font-size: 14px !important;
            }

            /* Larger buttons */
            .swal2-confirm-btn, .swal2-cancel-btn {
                font-size: 14px !important;
                padding: 8px 20px !important;
            }
        </style>
    </head>

   <body class="<?= AdminLteHelper::skinClass() ?> hold-transition sidebar-mini">
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>

        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

    </div>

    <?php echo  \app\components\FlashMessage::getMessage() ?>

    <script>
        let currentSwalText = null;

        function showMessage(type, text) {
            const isToast = (type === 'success' || type === 'info');
            const isSticky = isToast;

            // Check if the same toast is already shown
            const isSameRepeated = isToast && text === currentSwalText;

            // If same message is open, close first and re-open after slight delay
            if (isSameRepeated && Swal.isVisible()) {
                Swal.close();
                setTimeout(() => showMessage(type, text), 100); // Recursive re-call with delay
                return;
            }

            currentSwalText = isToast ? text : null;

            Swal.fire({
                position: isToast ? 'top-end' : 'center',
                icon: type,
                title: text,
                showConfirmButton: !isSticky,
                timer: isSticky ? undefined : 3000,
                timerProgressBar: !isSticky,
                toast: isToast,
                customClass: {
                    popup: 'swal2-custom-popup',
                    title: 'swal2-custom-title',
                    htmlContainer: 'swal2-custom-text'
                },
                showClass: {
                    popup: `
                    animate__animated
                    animate__headShake
                    animate__faster
                `
                },
                hideClass: {
                    popup: `
                    animate__animated
                    animate__fadeOutDown
                    animate__faster
                `
                }
            });
        }
    </script>


    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
