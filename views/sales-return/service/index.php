<?php

use app\components\CustomerUtility;
use app\models\Client;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model app\models\SalesReturn */

$this->title = Yii::t('app', 'Service or Repair');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Return'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Url::base(true).'/lib/js/sales-return/service.js', ['depends'=>\yii\web\JqueryAsset::className()]);

?>
<?php
    Modal::begin([
        'options' => [
            'id' => 'modal',
            'tabindex' => false,
        ],
        'clientOptions'=>[
            'backdrop' => 'static',
            'keyboard' => false,
        ],
        'header' => "<b style='margin:0; padding:0;'> Return Quantity </b>",
        'closeButton' => ['id' => 'close-button'],
        'size'=>Modal::SIZE_DEFAULT

    ]);
    echo '<div id="modalContent"></div>';
    Modal::end();
?>


<?php Pjax::begin(['enablePushState' => false, 'id'=>'returnCart',  'timeout' => 10000,]); ?>

    <div class="row">

        <div class="col-md-9">

            <div class="box box-success">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Invoice Items</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sold-invoice-items">
                    <?=
                    $this->render('sales_details', [
                        'dataProvider'=>$salesDataProvider,
                    ]);
                    ?>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Return Items</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="return-items">
                    <?=
                    $this->render('return_details', [
                        'dataProvider'=>$returnDataProvider,
                    ]);
                    ?>
                </div>
            </div>

        </div>

        <div class="col-md-3">
            <div class="box box-warning">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Invoice Details</h3>
                </div>

                <div class="box-body p-0">
                    <?= $this->render('_invoice', ['model'=>$salesInvoiceModel]); ?>
                </div>
            </div>

            <div class="box box-danger">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Service Or Repair</h3>
                </div>

                <div class="box-body p-0">
                    <?= $this->render('_service_repair', ['model'=>$salesReturnModel,]); ?>
                </div>
            </div>
        </div>
    </div>


<?php Pjax::end(); ?>