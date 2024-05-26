<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\models\Bank;
use app\models\Branch;
use app\models\PaymentType;
use dosamigos\qrcode\QrCode;
use yii\helpers\Json;
use yii\helpers\Url;



/* @var $model app\models\ClientPaymentHistory */
/* @var $details app\models\ClientPaymentDetails */

$invoiceTotal = 0;
$withdrawTotal = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <script type="text/javascript">
        //window.print();
        //window.onfocus=function(){ window.close();}
    </script>
</head>

<body>

<header class="clearfix">
    <div id="logo">
        <img height="70px" src="<?= Url::base(true) . '/images/'.SystemSettings::getLogo(); ?>">
    </div>

    <div class="barcode" style="width: 50%; float:left; margin-left: 5%;">
        <?php $generator = new \Picqer\Barcode\BarcodeGeneratorPNG(); ?>
        <div style="border: 1px solid #DDD; text-align: center; margin: 0 22%;">
            <p style="border-bottom: 1px solid #DDD; padding: 0; font-size: 14px; line-height: 28px; font-weight: bold">PAYMENT REFUND</p>
            <img src="<?= 'data:image/png;base64,' . base64_encode($generator->getBarcode($withdraw->id, $generator::TYPE_CODE_128)) ?>">
            <p style="font-size: 8px; color: #777; margin-bottom: 1px;"><?= $withdraw->id ?></p>
        </div>
    </div>


    <div id="company">
        <h2 class="name"><?= SystemSettings::getStoreName()?></h2>
        <div><?= SystemSettings::getAddress1()?></div>
        <div><?= SystemSettings::getAddress2()?></div>
        <div><a href="#"><?= SystemSettings::getContactNumber()?></a></div>
    </div>
</header>

<main>

    <div id="details" class="clearfix">
        <div id="client">
            <div class="to">REFUND TO:</div>
            <h2 class="name"><?= $model->customer->client_name ?></h2>
            <h2 class="name"><?= $model->customer->client_contact_number ?></h2>
        </div>
        <div id="invoice">
            <h1><b>REFUND ID:</b> <?= $withdraw->id ?></h1>
            <div class="verified" style="padding: 0;"><b>Type: </b><?= $model->user->username;?></div>
            <div class="date"><b>Date of Received:</b> <?= DateTimeUtility::getDate($model->received_at, SystemSettings::getDateFormat()) ?></div>
        </div>
    </div>

    <h5> DESCRIPTION</h5>

    <table id="payment-adjustment-table" border="0" cellspacing="0" cellpadding="0">

        <tr>
            <td>Type</td>
            <td>
                <?= strtoupper($withdraw->type) ?>
            </td>
        </tr>

        <tr>
            <td>Remarks</td>
            <td>
                <?= $model->remarks ?>
            </td>
        </tr>

        <tr>
            <td>Amount</td>
            <td>
                <?= Yii::$app->formatter->asDecimal($withdraw->amount)?>
            </td>
        </tr>

    </table>

    <table id="sign" style="" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>

                </td>

                <td style="text-align: center;">
                    <b><i><?= $model->user->first_name. ' '.$model->user->last_name;?></i></b>
                </td>

                <td>

                </td>
            </tr>

            <tr style="border-bottom: none;">
                <td style="text-align: left;">
                    Receiver's signature
                </td>
                <td style="text-align: center;">
                    Created by
                </td>
                <td style="text-align: right;">
                    Authorized signature
                </td>
            </tr>
        </table>

    <?php
        if(!empty(SystemSettings::invoiceFooterMassage())){
            //echo AppConfig::getInvoiceFooterMassage();
        }
    ?>


</main>

</body>
</html>