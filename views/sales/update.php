<?php

use app\components\Utility;
use yii\helpers\Html;

use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $salesDraft app\models\SalesDraft */

$this->title = Yii::t('app', 'Sales Update - Invoice (' . $model->sales_id . ')');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    var outletId='" . Utility::encrypt($model->outletId) . "';
    var checkAvailable='" . Url::base(true) . '/' . Yii::$app->controller->id . '/check-available-product' . "';
    var customerDetails='" . Url::base(true) . '/' . Yii::$app->controller->id . '/customer-details' . "';
    ", View::POS_END, 'checkAvailableProduct'
);

$this->registerJsFile(Url::base(true) . '/lib/js/salesUpdate.js', ['depends' => JqueryAsset::className()]);

?>

<div class="product-sales-items-draft-create">

    <?php Pjax::begin(['enablePushState' => false, 'id' => 'sellUpdate', 'timeout' => 10000,]); ?>

    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Product</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sales_product_details">
                    <?= $this->render('update/product', ['model' => $salesDraft, 'dataProvider' => $salesDraftDataProvider,]) ?>
                </div>
            </div>
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Invoice</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sales_product_details">
                    <?= $this->render('update/details', ['dataProvider' => $salesDraftDataProvider,]) ?>
                </div>
            </div>
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Invoice(Removed)</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sales_product_details">
                    <?= $this->render('update/remove', ['dataProvider' => $salesDraftRemoveDataProvider,]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Customer</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sales_product_details">
                    <?= $this->render('update/customer', ['model' => $model]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php Pjax::end(); ?>

</div>

