<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\Bank;
use app\models\Branch;
use app\models\ClientPaymentHistory;
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
            <p style="border-bottom: 1px solid #DDD; padding: 0; font-size: 14px; line-height: 28px; font-weight: bold">PAYMENT RECEIPT</p>
            <img src="<?= 'data:image/png;base64,' . base64_encode($generator->getBarcode($model->client_payment_history_id, $generator::TYPE_CODE_128)) ?>">
            <p style="font-size: 8px; color: #777; margin-bottom: 1px;"><?= $model->client_payment_history_id ?></p>
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
            <div class="to">RECEIVED FROM:</div>
            <h2 class="name"><?= $model->customer->client_name ?></h2>
            <h2 class="name"><?= $model->customer->client_contact_number ?></h2>
        </div>


        <div id="invoice">
            <h1><b>Transaction</b> <?= $model->client_payment_history_id ?></h1>
            <div class="verified" style="padding: 0;"><b>Type: </b><?= $model->user->username;?></div>
            <div class="date"><b>Date of Received:</b> <?= DateTimeUtility::getDate($model->received_at, SystemSettings::getDateFormat()) ?></div>
        </div>
    </div>

    <h6> DESCRIPTION</h6>

    <table id="payment-adjustment-table" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>Received Amount</td>
            <td>
                <?= Yii::$app->formatter->asDecimal($model->received_amount).' '.SystemSettings::getAppCurrency() ?>
            </td>
        </tr>


        <tr>
            <td>Available Amount</td>
            <td>
                <?= Yii::$app->formatter->asDecimal($model->remaining_amount).' '.SystemSettings::getAppCurrency() ?>
            </td>
        </tr>


        <tr>
            <td>Purpose</td>
            <td>
                <?= strtoupper($model->received_type) ?>
            </td>
        </tr>


        <tr>
            <td>Type</td>
            <td>
                <?php

                    if($model->received_type== ClientPaymentHistory::RECEIVED_TYPE_SALES_RETURN){
                        echo strtoupper('N/A');
                    }else{
                        if($model->paymentType->payment_type_name== PaymentType::TYPE_CASH){
                            echo strtoupper(PaymentType::TYPE_CASH);
                        }else{
                            $object = Json::decode($model->extra);
                            $bank = Bank::findOne($object['bank_id']);
                            $branch = Branch::findOne($object['branch_id']);
                            echo PaymentType::TYPE_DEPOSIT." ( ".$bank->bank_name. ", ".$branch->branch_name.")";
                        }
                    }


                ?>
            </td>
        </tr>

        <tr>
            <td>Remarks</td>
            <td>
                <?= $model->remarks ?>
            </td>
        </tr>

    </table>

    <?php if (count($details)>0):?>

        <h6>SETTLEMENT - INVOICE</h6>

        <table border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
            <thead>
            <tr>
                <th class="no">#</th>
                <th class="item">TIME</th>
                <th class="brand">INVOICE</th>
                <th class="size">PAYMENT</th>
                <th class="total">AMOUNT</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($details as $index => $detail): ?>
            <tr>
                <td class="no"><?= $index + 1 ?></td>

                <td class="item"><?= $detail->created_at ?></td>
                <td class="brand"><?= $detail->sales_id ?></td>
                <td class="size"><?= strtoupper($detail->payment_type )?></td>
                <td class="total"><?= Yii::$app->formatter->asDecimal($detail->paid_amount) ?></td>
                <?php  $invoiceTotal+=$detail->paid_amount?>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <table id="summery-table" border="0" cellspacing="0" cellpadding="0" width="100%">

            <tr>
                <td width="85%"><b>Total Amount</b></td>
                <td colspan="1"><?= Yii::$app->formatter->asDecimal($invoiceTotal).' '.SystemSettings::getAppCurrency() ?></td>
            </tr>
        </table>

    <?php endif;?>

    <?php if (count($withdraw)>0):?>

        <h6>SETTLEMENT - REFUND</h6>

        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
            <tr>

                <th class="no">#</th>
                <th class="item">TIME</th>
                <th class="item">ID</th>
                <th class="brand">REMARKS</th>
                <th class="size">TYPE</th>
                <th class="total">AMOUNT</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($withdraw as $index => $detail): ?>
                <tr>
                    <td class="no"><?= $index + 1 ?></td>
                    <td class="item"><?= $detail->created_at ?></td>
                    <td class="item"><?= $detail->id ?></td>
                    <td class="brand"><?= $detail->remarks ?></td>
                    <td class="size">
                        <?php

                            $object = Json::decode($detail->extra);

                            if($object['paymentType']==PaymentType::TYPE_CASH){
                                echo strtoupper(PaymentType::TYPE_CASH);
                            }else{
                                $bank = Bank::findOne($object['Bank']);
                                $branch = Branch::findOne($object['Branch']);
                                echo $bank->bank_name. " (".$branch->branch_name.")";
                            }
                        ?></td>
                    <td class="total"><?= Yii::$app->formatter->asDecimal($detail->amount) ?></td>
                    <?php  $withdrawTotal+=$detail->amount?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <table id="summery-table" border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
                <td width="85%"><b>Total Amount</b></td>
                <td colspan="1"><?= Yii::$app->formatter->asDecimal($withdrawTotal).' '.SystemSettings::getAppCurrency() ?></td>
            </tr>
        </table>
    <?php endif;?>


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

    <?php
        if(!empty(SystemSettings::invoiceFooterMassage())){
            //echo AppConfig::getInvoiceFooterMassage();
        }
    ?>


</main>

</body>
</html>