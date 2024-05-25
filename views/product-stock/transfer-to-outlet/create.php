<?php

use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsDraft */
$this->title = Yii::t('app', 'Stock Transfer To Outlet');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("var productPrice='".Url::base(true).'/'.Yii::$app->controller->id.'/get-product-price'."';", View::POS_END, 'getProductPrice');
$this->registerJsFile(Url::base(true).'/lib/js/stockMovement.js', ['depends'=> JqueryAsset::className()]);
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
    'header' => "<b style='margin:0; padding:0;'> Details </b>",
    'closeButton' => ['id' => 'close-button'],
    'size'=>Modal::SIZE_LARGE

]);
echo '<div id="modalContent"></div>';
Modal::end();
?>

<div class="product-stock-items-draft-create">
    <div class="row">
        <?php Pjax::begin(['enablePushState' => false, 'id'=>'stock']); ?>

        <div class="col-md-9">

            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Product</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="sales_product_details">
                    <?= $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]) ?>
                </div>
            </div>

        </div>

        <?php Pjax::end(); ?>



        <div class="col-md-3">

            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Details</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="product_details">
                    <?= $this->render('_details', [
                        'model' => $model,
                        'productStock'=>$productStock,
                    ]) ?>
                </div>
            </div>

        </div>

    </div>


</div>
