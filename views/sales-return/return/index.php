<?php

use app\components\CustomerUtility;
use app\models\Client;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model app\models\SalesReturn */

$this->title = Yii::t('app', 'Return');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Url::base(true).'/js/sales-return.js', ['depends'=> JqueryAsset::className()]);
?>

    <style>
        .panel {
            margin-bottom: 0px !important;
        }

        .break {
            margin-top: 10px;
        }

    </style>

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
        <div class="col-md-8">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Invoice Items</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sold-invoice-items">
                    <?=
                    $this->render('salesDetails', [
                        'dataProvider'=>$salesDataProvider,
                    ]);
                    ?>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Return Items</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="return-items">
                    <?=
                    $this->render('returnDetails', [
                        'dataProvider'=>$returnDataProvider,
                    ]);
                    ?>
                </div>
            </div>

        </div>

        <div class="col-md-4">

            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Invoice</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sales_product_details">
                    <?= $this->render('_invoice', ['model'=>$model]); ?>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Refund</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="return-amount-details">
                    <?=
                    $this->render('_customer', [
                        'model'=>$salesReturn,
                    ]);
                    ?>
                </div>
            </div>

        </div>
    </div>

<?php Pjax::end(); ?>