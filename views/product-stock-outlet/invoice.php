<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\models\ProductStock;
use app\models\ProductStockOutlet;
use dosamigos\qrcode\QrCode;
use Picqer\Barcode\BarcodeGeneratorPNG;
use yii\helpers\Json;
use yii\helpers\Url;


/* @var $model app\models\ProductStockOutlet */
/* @var $product app\models\ProductStockItemsOutlet */
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript">
        //window.print();
        //window.onfocus=function(){ window.close();}
    </script>
</head>

<body>

<?php
$generator = new BarcodeGeneratorPNG();
?>

<header class="clearfix">
    <div style="width: 100%">
        <div id="logo" style="width: 20%; float:left;">
            <img height="70px" src="<?= Url::base(true) . '/images/' . SystemSettings::getLogo(); ?>">
        </div>

        <div class="barcode" style="width: 50%; float:left; margin-left: 5%;">

            <div style="border: 1px solid #DDD; text-align: center; margin: 0 22%;">
                <p style="border-bottom: 1px solid #DDD; padding: 0; font-size: 14px; line-height: 28px; font-weight: bold">
                    INVOICE</p>
                <img src="<?= 'data:image/png;base64,' . base64_encode($generator->getBarcode($model->invoice, $generator::TYPE_CODE_128)) ?>">
                <p style="font-size: 8px; color: #777; margin-bottom: 1px;"><?= $model->invoice ?></p>
            </div>
        </div>

        <div id="company" style="width: 25%; float:left;">
            <h2 class="name"><strong><?= SystemSettings::getStoreName() ?></strong></h2>
            <div><?= SystemSettings::getAddress1() ?></div>
            <div><?= SystemSettings::getAddress2() ?></div>
            <div><?= SystemSettings::getContactNumber() ?></div>
        </div>

    </div>
</header>

<main>

    <div id="details" class="clearfix">
        <div id="client">
            <h2 style="font-size: 15px" class="name"><?= $model->invoice?></h2>
            <div>
                <b>PRINT</b> <?= DateTimeUtility::getDate($model->createdAt, SystemSettings::getDateFormat()) ?>
            </div>
        </div>
        <div id="invoice">
            <h1><b>TYPE</b> <?= strtoupper($model->type) ?></h1>
            <div class="verified" style="padding: 0;">

                <?php
                if ($model->transferFrom === ProductStockOutlet::TRANSFER_FROM_STOCK) {
                    echo  "<b>FROM </b>".ProductStockOutlet::TRANSFER_FROM_STOCK;
                } else {
                    echo  "<b>FROM </b>".$model->transferOutletDetail->name;
                }
                ?>

            </div>
            <div class="verified" style="padding: 0;">

                <?php
                if ($model->receivedFrom === ProductStockOutlet::TRANSFER_FROM_STOCK) {
                    echo  "<b>TO </b>".ProductStockOutlet::TRANSFER_FROM_STOCK;
                } else {
                    echo  "<b>TO </b>".$model->receivedOutletDetail->name;
                }
                ?>

            </div>
            <div class="date">
                <div><b>STATUS </b><?= strtoupper($model->status) ?></div>
            </div>
        </div>
    </div>


    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="no">#</th>
            <th class="item">ITEM</th>
            <th class="brand">BRAND</th>
            <th class="size">SIZE</th>
            <th class="unit">COST</th>
            <th class="unit">WHOLESALE</th>
            <th class="unit">RETAIL</th>
            <th class="qty">PRV.QUANTITY</th>
            <th class="qty">NEW.QUANTITY</th>
            <th style="text-align: right">TOTAL</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($items as $index => $product): ?>
            <tr>
                <td class="no"><?= $index + 1 ?></td>
                <td class="item"><?= $product->item->item_name ?></td>
                <td class="brand"><?= $product->brand->brand_name ?></td>
                <td class="size"><?= $product->size->size_name ?></td>
                <td class="unit"><?= !empty($product->cost_price)?Yii::$app->formatter->asDecimal($product->cost_price):'' ?></td>
                <td class="unit"><?= !empty($product->wholesale_price)?Yii::$app->formatter->asDecimal($product->wholesale_price):'' ?></td>
                <td class="unit"><?= !empty($product->retail_price)?Yii::$app->formatter->asDecimal($product->retail_price):'' ?></td>
                <td class="unit"><?= Yii::$app->formatter->asDecimal($product->previous_quantity) ?></td>
                <td class="unit"><?= Yii::$app->formatter->asDecimal($product->new_quantity) ?></td>
                <td class="qty"><?= Yii::$app->formatter->asDecimal($product->total_quantity) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


    <table id="sign" style="" border="0" cellspacing="0" cellpadding="0">
        <tr>

            <td style="text-align: left;">
                <b><i><?= (!empty($model->transferByUser)) ? strtoupper($model->transferByUser->username) : '' ?></i></b>
            </td>

            <td style="text-align: right;">
                <b><i><?= (!empty($model->receivedBy)) ? strtoupper($model->receivedByUser->username) : '' ?></i></b>
            </td>

            <td>

            </td>
        </tr>

        <tr style="border-bottom: none;">
            <td style="text-align: left;">
                Transfer By
            </td>
            <td style="text-align: right;">
                Received By
            </td>
        </tr>
    </table>

</main>

</body>
</html>