<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model app\models\MarketBook */

$this->title = Yii::t('app', 'Create Market Book');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Market Books'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    var checkAvailable='".Url::base(true).'/'.Yii::$app->controller->id.'/check-available-product'."';
    var customerDetails='".Url::base(true).'/'.Yii::$app->controller->id.'/customer-details'."';", View::POS_END, 'checkMarketAvailableProduct'
);

$this->registerJsFile(Url::base(true).'/js/marketBook.js', ['depends'=> JqueryAsset::className()]);
//$this->registerJsFile(Url::base(true).'/js/salesDraftUpdate.js', ['depends'=>\yii\web\JqueryAsset::className()]);

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
        'header' => "<b style='margin:0; padding:0;'> Details </b>",
        'closeButton' => ['id' => 'close-button'],
        'size'=>Modal::SIZE_DEFAULT

         ]);
        echo '<div id="modalContent"></div>';
    Modal::end();
?>



<div class="market-book-create">

    <?php Pjax::begin(['enablePushState' => false, 'id'=>'marketSell',  'timeout' => 10000,]); ?>

    <div class="row">
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Product</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="size-create">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Customer</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body" id="size-create">
                    <?= $this->render('customerDetails', [
                        'model' => $client,
                    ]) ?>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Invoice</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <?= $this->render('details', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
                ]) ?>
                </div>
            </div>
        </div>

    <?php Pjax::end(); ?>

</div>




