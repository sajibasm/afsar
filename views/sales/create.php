<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsDraft */

$this->title = Yii::t('app', 'New Sell Point');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    var outletId='". Utility::encrypt($model->outletId)."'
    var checkAvailable='".Url::base(true).'/'.Yii::$app->controller->id.'/check-available-product'."';
    var customerDetails='".Url::base(true).'/'.Yii::$app->controller->id.'/customer-details'."';
    ", View::POS_END, 'checkAvailableProduct'
);

$this->registerJsFile(Url::base(true).'/lib/js/sales.js', ['depends'=>\yii\web\JqueryAsset::className()]);

?>

<style>
    .panel {
        margin-bottom: 0px !important;
    }

    .break {
        margin-top: 10px;
    }

</style>

<div class="product-sales-items-draft-create">

    <div class="row">

        <?php Pjax::begin(['enablePushState' => false, 'id'=>'sell',  'timeout' => 10000,]); ?>

        <div class="col-md-8">

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Product</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sales_product_details">
                    <?= $this->render('sales-draft/_product', ['model'=>$salesDraft, 'dataProvider' => $salesDraftDataProvider,]) ?>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Invoice</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sales_product_invoice">
                    <?= $this->render('sales-draft/details', ['dataProvider'=>$salesDraftDataProvider,]) ?>
                </div>
            </div>

        </div>

        <div class="col-md-4">

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Payment</h3>
                </div>
                <div class="box-body" id="sales_customer">
                    <?= $this->render('_form', ['model'=>$model]) ?>
                </div>
            </div>

        </div>

        <?php Pjax::end(); ?>
    </div>



</div>
