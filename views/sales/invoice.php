<?php

use app\components\QrCodeGenerator;
use app\components\SystemSettings;
use app\components\DateTimeUtility;
use yii\helpers\Url;

$this->title = 'Invoice';


/* @var $model app\models\Sales */
/* @var $salesDetails app\models\SalesDetails */
/* @var $qrCode string */


// Generate public Invoice Lookup URL
$encryptedId = \app\components\Utility::encrypt($model->sales_id);
$publicUrl = Url::to(['sales/invoice-lookup', 'id' => $encryptedId], true);
$qrCodeUrl = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=' . urlencode($publicUrl);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html"/>
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
            <img height="70px" src="<?= Yii::getAlias('@webroot/images/' . $outlet->logo) ?>">
        </div>

        <div class="barcode" style="width: 50%; float:left; margin-left: 5%;">
            <div style="border: 0px solid #DDD; text-align: center;">
                <?php if (!empty($qrCode)): ?>
                    <img src="<?= $qrCode ?>" alt="QR Code" style="width: 120px; height: 120px;">
                    <div style="font-size: 10px; color: #555; margin-top: 0px;">Scan to view invoice online</div>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin: 5px 0;">  <!-- Top & bottom space -->
                <span style="
                display: inline-block;
                border: 1px solid #333;
                padding: 12px 25px;
                font-size: 16px;
                line-height: 1.6;
                letter-spacing: 0px;   /* More space between characters */
            ">
            <b style="padding: 10px 5px;">Invoice Number: <?= htmlspecialchars($model->sales_id) ?></b>
            </span>
            </div>

        </div>

        <div id="company" style="width: 25%; float:left;">
            <h2 class="name"><strong><?= strtoupper(SystemSettings::getStoreName()) ?></strong></h2>
            <h2 class="name"><?= $outlet->name ?></h2>
            <div><?= $outlet->address1 ?></div>
            <div><?= $outlet->address2 ?></div>
            <div>Contact Number: <?= $outlet->contactNumber ?></div>
            <div>Email: <?= $outlet->email ?></div>
        </div>
    </div>
</header>

<main>


    <div id="details" class="clearfix">
        <div id="client" style="font-size: 12px; line-height: 1.6;">
            <div>Customer Name: <?= htmlspecialchars($model->client_name) ?></div>

            <div>
                Address:
                <?= htmlspecialchars($model->client->client_address1) ?>
                <?= !empty($model->client->clientCity->city_name) ? ', ' . htmlspecialchars($model->client->clientCity->city_name) : '' ?>
            </div>

            <div>Phone: <?= htmlspecialchars($model->client->client_contact_number) ?></div>
        </div>
        <div id="invoice" style="font-size: 12px; line-height: 1.6;">
            <div class="date">
                Sales Date: <?= DateTimeUtility::getTime($model->created_at, SystemSettings::getDateFormat()) ?>
            </div>
            <div class="verified" style="padding: 0;">
                Approved By: <?= isset($model->authorized->username) ? strtoupper($model->authorized->username) : ''; ?>
            </div>
        </div>
    </div>

    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="no"><b>SL#</b></th>
            <th class="item"><b>ITEM</b></th>
            <th class="brand"><b>BRAND</b></th>
            <th class="size"><b>SIZE</b></th>
            <th class="unit"><b>UNIT PRICE</b></th>
            <th class="qty"><b>QUANTITY</b></th>
            <th style="text-align: right"><b>TOTAL</b></th>
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


    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none;">
        <tr style="border: none;">
            <!-- Left side: Previous Dues -->
            <td width="30%" valign="top" style="border: none;">
                <?php if ($previousDues > 0): ?>
                    <table cellspacing="0" cellpadding="4" width="100%" style="border-collapse: collapse;">
                        <tr>
                            <td colspan="2" style="font-size: 14px;">
                                <b>Due Summary</b>
                            </td>
                        </tr>
                        <tr>
                            <td width="70%" style="font-size: 14px;">
                                Previous Dues
                            </td>
                            <td style="text-align: right; white-space: nowrap; font-size: 14px;">
                                <?= Yii::$app->formatter->asDecimal($previousDues) ?> 
                            </td>
                        </tr>
                        <tr>
                            <td width="70%" style="font-size: 14px;">
                                Current Dues
                            </td>
                            <td style="text-align: right; white-space: nowrap; font-size: 14px;">
                                <?= Yii::$app->formatter->asDecimal($model->due_amount - $model->reconciliation_amount) ?> 
                            </td>
                        </tr>
                        <tr>
                            <td width="70%" style="font-size: 14px;">
                                <b>Total Dues</b>
                            </td>
                            <td style="text-align: right; white-space: nowrap; font-size: 14px;">
                                <b><?= Yii::$app->formatter->asDecimal($previousDues + ($model->due_amount - $model->reconciliation_amount)) ?> </b>
                            </td>
                        </tr>
                    </table>


                <?php endif; ?>
            </td>

            <!-- Right side: Current Summary -->
            <td width="70%" valign="top" style="border: none;">
                <table id="summery-table" cellspacing="0" cellpadding="4" width="100%" style="border: none;">
                    <tr>
                        <td width="70%" style="font-size: 14px; border: none;"><b>SubTotal</b></td>
                        <td width="30%" style="text-align: right; white-space: nowrap; font-size: 14px; border: none;">
                            <?= Yii::$app->formatter->asDecimal($model->total_amount - ($model->vat_amount+$model->advance_income_tax_amount) ) ?> 
                        </td>
                    </tr>

                    <tr>
                        <td style="font-size: 14px; border: none;">Discount</td>
                        <td style="text-align: right; white-space: nowrap; font-size: 14px; border: none;">
                            <?= Yii::$app->formatter->asDecimal($model->discount_amount*-1) ?> 
                        </td>
                    </tr>

                    <tr>
                        <td width="70%" style="font-size: 14px; border: none;">VAT (<?= SystemSettings::getVAT()?>%)</td>
                        <td width="30%" style="text-align: right; white-space: nowrap; font-size: 14px; border: none;">
                            <?= Yii::$app->formatter->asDecimal($model->vat_amount) ?> 
                        </td>
                    </tr>

                    <tr>
                        <td width="70%" style="font-size: 14px; border: none;">AIT (<?= SystemSettings::getAIT()?>%)</td>
                        <td width="30%" style="text-align: right; white-space: nowrap; font-size: 14px; border: none;">
                            <?= Yii::$app->formatter->asDecimal($model->advance_income_tax_amount) ?> 
                        </td>
                    </tr>

                    <tr>
                        <td style="font-size: 14px; border-top: 1px solid #666;"><b>Net Payable</b></td>
                        <td style="text-align: right; white-space: nowrap; font-size: 14px; border-top: 1px solid #666;">
                            <?= Yii::$app->formatter->asDecimal($model->total_amount - $model->discount_amount) ?> 
                        </td>
                    </tr>

                    <?php
                        $totalPaid = $model->paid_amount+$reconciliationAmount;
                    ?>

                    <tr>
                        <td style="font-size: 14px; border-top: 1px solid #666;">Paid/Advance</td>
                        <td style="text-align: right; white-space: nowrap; font-size: 14px; border-top: 1px solid #666;">
                            <?= Yii::$app->formatter->asDecimal($totalPaid) ?> 
                        </td>
                    </tr>

                    <?php if($reconciliationAmount > 0): ?>
                    <tr>
                        <td style="font-size: 14px; border: none;">Reconciliation</td>
                        <td style="text-align: right; white-space: nowrap; font-size: 14px; border: none;">
                            <?= Yii::$app->formatter->asDecimal($reconciliationAmount) ?> 
                        </td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <td style="font-size: 14px; border-top: 1px solid #666;"><b>Total Paid</b></td>
                        <td style="text-align: right; white-space: nowrap; font-size: 14px; border-top: 1px solid #666;">
                            <?php
                            $totalPaid = $model->paid_amount + $reconciliationAmount;
                            echo Yii::$app->formatter->asDecimal($totalPaid);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td style="font-size: 14px; border-top: 1px solid #666;">Dues</td>
                        <td style="text-align: right; white-space: nowrap; font-size: 13px; border-top: 1px solid #666;">
                            <?= Yii::$app->formatter->asDecimal($model->due_amount - $model->reconciliation_amount) ?> 
                        </td>
                    </tr>
                </table>



            </td>
        </tr>
    </table>


    <!-- In Word Block -->
    <?php
    $netPayable = $model->total_amount - $model->discount_amount;
    ?>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 10px; border: none;">
        <tr>
            <td style="border: none;"><b>In Word:</b> <?= \app\components\InvoiceGenerator::numberToTakaWords($netPayable) ?></td>
        </tr>
    </table>


    <table id="sign" style="" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>

            </td>

            <td style="text-align: center;">
                <b><i><?= $model->user->first_name . ' ' . $model->user->last_name ?></i></b>
            </td>

            <td>
                <b></b>
            </td>
        </tr>

        ApprovedBy
        <tr style="border-bottom: none;">
            <td style="text-align: left;">
                Customer's signature
            </td>
            <td style="text-align: center;">
                Prepared by
            </td>
            <td style="text-align: right;">
                Authorized Signature
            </td>
        </tr>
    </table>

    <?php echo SystemSettings::invoiceFooterMassage() ?>

</main>

</body>
</html>
