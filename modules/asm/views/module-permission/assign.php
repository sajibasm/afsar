<?php

use app\models\User;
use app\modules\asm\components\ASM;
use app\modules\asm\models\Modules;
use app\modules\asm\Module;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/* @var $this yii\web\View */
/* @var $model app\models\ModulePermission */
/* @var $modules app\models\Modules */
$this->title = 'Module Access Permission';

$this->registerCssFile(Yii::getAlias('@web/lib/css/checkbox2.css'), ['depends' => [BootstrapAsset::className()]]);
$this->registerJsFile(Url::base(true) . '/lib/js/modulePermission.js', ['depends' => JqueryAsset::className()]);
$permission = Yii::$app->asm->assignedPermission($model->userId);
?>

<style>
    .row-eq-height {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
    }  padding: 0;
    }
</style>

<div class="module-permission-create">




    <?php $form = ActiveForm::begin(); ?>


    <div class="row">
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Assign'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>


</div>

<?php ActiveForm::end(); ?>


</div>
