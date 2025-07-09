<?php

use yii\helpers\Html;

use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsDraft */

$this->title = Yii::t('app', 'Settle Invoice(s)');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Client Payment Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    var checkAvailable='".Url::base(true).'/'.Yii::$app->controller->id.'/check-available-product'."';
    var customerDetails='".Url::base(true).'/'.Yii::$app->controller->id.'/customer-details'."';
    ", View::POS_END, 'checkAvailableProduct'
);
$this->registerJsFile(Url::base(true).'/lib/js/client/settle-payment.js', ['depends'=> JqueryAsset::className()]);
?>

<div class="product-sales-items-draft-create">

    <?php Pjax::begin(['enablePushState' => false, 'id'=>'customerPaymentPay',  'timeout' => 10000,]); ?>

    <div class="row">
        <div class="col-md-9">

            <div class="box box-primary">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Invoice</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sales_product_details">
                    <?= $this->render('sales_details', ['dataProvider' => $dataProvider, 'model'=>$model]) ?>
                </div>
            </div>

        </div>

        <div class="col-md-3">
            <div class="box box-success">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Adjustment Method</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="customer_payment_history_pay">
                    <?= $this->render('_payment', ['model'=>$model]) ?>
                </div>
            </div>
        </div>
    </div>
    <?php Pjax::end(); ?>

</div>