<?php

use app\assets\SalesAsset;
use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsDraft */

$this->title = Yii::t('app', 'Sales Terminal');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsVar('outletId', Yii::$app->request->get('store')); // or any value
$this->registerJsVar('addCartItem', Url::to(['/sales/add-cart-item'], true));
$this->registerJsVar('checkAvailable', Url::to(['/sales/check-available-product'], true));
$this->registerJsVar('customerDetails', Url::to(['/sales/customer-details'], true));

$asset = SalesAsset::register($this);
$this->registerJsFile($asset->baseUrl . '/create.js', ['depends' => SalesAsset::class]);


$this->registerJs(<<<JS

// Success handler
$(document).on("editableSuccess.kv", function(event, val, form, data) {
    // if (data && data.message) {
    //     Swal.fire({
    //         icon: "success",
    //         title: "Success",
    //         text: data.message,
    //         timer: 1500,
    //         showConfirmButton: false
    //     });
    // }

    // Optionally reload PJAX to reflect updated total/other cells
    setTimeout(() => {
        $.pjax.reload({container: '#SellDraft', timeout: 10000});
    }, 300);
});

// Error handler
$(document).on("editableError.kv", function(event, val, form, data) {
    // if (data && data.message) {
    //     Swal.fire({
    //         icon: "error",
    //         title: "Error",
    //         text: data.message
    //     });
    // }
});
JS
    , \yii\web\View::POS_READY);



?>

<div class="product-sales-items-draft-create">

    <div class="row">
        <?php Pjax::begin(['id' => 'SellDraft']);?>
        <div class="col-md-8">
            <div class="box box-success">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Product</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sales_product_details">
                    <?= $this->render('product', ['model'=>$salesDraft, 'dataProvider' => $salesDraftDataProvider,]) ?>

                </div>
            </div>

            <div class="box box-warning">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Sales Items</h3>
                    <div class="box-tools pull-right"></div>
                </div>

                <div class="box-body" id="sales_product_invoice">
                    <?= $this->render('sales_items', ['dataProvider'=>$salesDraftDataProvider,]) ?>
                </div>
            </div>
        </div>


        <div class="col-md-4">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Payment</h3>
                </div>
                <div class="box-body" id="sales_customer">
                    <?= $this->render('_payment.php', ['model'=>$model]) ?>
                </div>
            </div>
        </div>
        <?php Pjax::end();?>
    </div>


</div>
