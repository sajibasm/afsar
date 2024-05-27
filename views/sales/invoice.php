<?php

use app\components\QrCodeGenerator;
use app\components\SystemSettings;
use app\components\DateTimeUtility;
use yii\helpers\Url;

$this->title = 'Invoice';


/* @var $model app\models\Sales */
/* @var $salesDetails app\models\SalesDetails */
/* @var $qrCode string */
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html" />
    <meta charset="UTF-8">
</head>

<body>

<?php
    $outlet = $model->outlet;
    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
?>

<header class="clearfix">
    <div style="width: 100%">

    <div id="logo" style="width: 20%; float:left;">
        <img height="70px" src="<?= Url::base(true) . '/images/'.$outlet->logo; ?>">
    </div>

    <div class="barcode" style="width: 50%; float:left; margin-left: 5%;">
        <div style="border: 1px solid #DDD; text-align: center; margin: 0 22%;">
            <p style="border-bottom: 1px solid #DDD; padding: 0; font-size: 14px; line-height: 28px; font-weight: bold"><?= strtoupper(SystemSettings::getStoreName())?></p>
            <?= QrCodeGenerator::generate($model->sales_id) ?>
        </div>
    </div>

    <div id="company" style="width: 25%; float:left;">
        <?= strtoupper(SystemSettings::getStoreName())?>
        <h2 class="name"><strong><?= $outlet->name ?></strong></h2>
        <div><?= $outlet->address1 ?></div>
        <div><?= $outlet->address2 ?></div>
        <div><?= $outlet->contactNumber ?></div>
    </div>

    </div>
</header>

<main>

    <div id="details" class="clearfix">
        <div id="client">
            <h2  style="font-size: 15px" class="name"><?= $model->client_name ?></h2>
            <div><?= $model->client->client_address1.', '.$model->client->clientCity->city_name ?></div>
            <div><?= $model->client->client_contact_number?></div>
        </div>
        <div id="invoice">
            <h1><b>INVOICE</b> <?= $model->sales_id ?></h1>
            <div class="verified" style="padding: 0;"><b>VERIFIED BY </b><?= isset($model->authorized->username)?strtoupper($model->authorized->username):'';?></div>
            <div class="date"><b>DATE OF INVOICE</b> <?= DateTimeUtility::getDate($model->created_at, SystemSettings::getDateFormat()) ?></div>
        </div>
    </div>


    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="no">#</th>
            <th class="item">ITEM</th>
            <th class="brand">BRAND</th>
            <th class="size">SIZE</th>
            <th class="unit">UNIT PRICE</th>
            <th class="qty">QUANTITY</th>
            <th style="text-align: right">TOTAL</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($salesDetails as $index => $product): ?>
        <tr>
            <td class="no"><?= $index + 1 ?></td>
            <td class="item"><?= $product->item->item_name ?></td>
            <td class="brand"><?= $product->brand->brand_name ?></td>
            <td class="size"><?= $product->size->size_name ?></td>
            <td class="unit"><?= Yii::$app->formatter->asDecimal($product->sales_amount) ?></td>
            <td class="qty"><?= $product->quantity ?></td>
            <td><?= Yii::$app->formatter->asDecimal($product->total_amount) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <table id="summery-table" border="0" cellspacing="0" cellpadding="0" width="100%">

        <tr>
            <td width="85%"><b>Total Amount</b></td>
            <td colspan="1"><?= Yii::$app->formatter->asDecimal($model->total_amount).' '.SystemSettings::getAppCurrency() ?></td>
        </tr>
        <tr>
            <td width="85%">Less/Discount</td>
            <td><?= Yii::$app->formatter->asDecimal($model->discount_amount).' '.SystemSettings::getAppCurrency() ?></td>
        </tr>

        <tr>
            <td width="85%"><b>Net Payable</b></td>
            <td><?= Yii::$app->formatter->asDecimal($model->total_amount - $model->discount_amount).' '.SystemSettings::getAppCurrency()?></td>
        </tr>

        <tr>
            <td width="85%">Paid/Advance</td>
            <td><?= Yii::$app->formatter->asDecimal($model->paid_amount+$invisibleReconciliationAmount).' '.SystemSettings::getAppCurrency() ?></td>
        </tr>

        <tr>
            <td width="85%">Dues</td>
            <td><?= Yii::$app->formatter->asDecimal($model->due_amount-$model->reconciliation_amount).' '.SystemSettings::getAppCurrency() ?></td>
        </tr>

    </table>

    <?php if( ($model->sales_return_amount>0 || $visibleReconciliationAmount>0) || $model->received_amount>$model->paid_amount):?>



        <table id="payment-adjustment-table" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="3">
                <b>SETTLEMENT</b>
                </td>
            </tr>

            <tr>
                <td>Total Amount</td>
                <td colspan="2"><?= Yii::$app->formatter->asDecimal($model->total_amount - $model->discount_amount).' '.SystemSettings::getAppCurrency() ?></td>
            </tr>

            <tr>
                <td>Paid/Advance</td>
                <td colspan="2">
                    <?= Yii::$app->formatter->asDecimal($model->paid_amount+$invisibleReconciliationAmount).' '.SystemSettings::getAppCurrency() ?>
                </td>
            </tr>

            <?php if ($visibleReconciliationAmount>0):?>
            <tr>
                <td>Reconciliation</td>
                <td style="text-align: center">
                    <?= $reconciliationType; ?>
                </td>
                <td>
                    <?= Yii::$app->formatter->asDecimal($visibleReconciliationAmount).' '.SystemSettings::getAppCurrency() ?>
                </td>
            </tr>
            <?php endif; ?>

            <?php if($model->received_amount>$model->paid_amount): ?>
            <tr>
                <td>Cash/Bank</td>
                <td colspan="2"><?= Yii::$app->formatter->asDecimal($model->received_amount - $model->paid_amount).' '.SystemSettings::getAppCurrency() ?></td>
            </tr>
            <?php endif;?>

            <?php if ($model->sales_return_amount>0):?>
            <tr>
                <td>Sales Return</td>
                <td colspan="2"><?= Yii::$app->formatter->asDecimal($model->sales_return_amount).' '.SystemSettings::getAppCurrency() ?></td>
            </tr>
            <?php endif;?>

            <?php
                $totalAmount = $model->total_amount-$model->discount_amount;
                $totalReceivedAmount = $model->received_amount+$model->reconciliation_amount+$model->sales_return_amount;
                if($totalReceivedAmount>$totalAmount){
                    $totalDues = 0;
                }else{
                    $totalDues = $totalAmount - $totalReceivedAmount;
                }
            ?>

            <?php if($totalReceivedAmount>$totalAmount):?>
                    <tr>
                        <td>Account Deposit(Refund) </td>
                        <td colspan="2"><?= Yii::$app->formatter->asDecimal( $totalAmount - $totalReceivedAmount).' '.SystemSettings::getAppCurrency() ?></td>
                    </tr>
            <?php endif;?>

            <tr>
                <td>Dues</td>
                <td colspan="2"><?= Yii::$app->formatter->asDecimal($totalDues).' '.SystemSettings::getAppCurrency() ?></td>
            </tr>

        </table>
    <?php endif;?>


    <table id="sign" style="" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>

                </td>

                <td style="text-align: center;">
                    <b><i><?= strtoupper($model->user->first_name. ' '.$model->user->last_name)?></i></b>
                </td>

                <td>

                </td>
            </tr>

            <tr style="border-bottom: none;">
                <td style="text-align: left;">
                    Customer's signature
                </td>
                <td style="text-align: center;">
                    Prepared by
                </td>
                <td style="text-align: right;">
                    Authorized signature
                </td>
            </tr>
        </table>

    <?php echo SystemSettings::invoiceFooterMassage()?>

</main>

</body>
</html>
