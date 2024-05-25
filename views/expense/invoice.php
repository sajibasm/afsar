<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\Expense */
/* @var $form yii\widgets\ActiveForm */

?>


<!DOCTYPE html>
<html lang="ar">
<!-- <html lang="ar"> for arabic only -->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>Express Invoice</title>
    <style>
        @media print {
            @page {
                margin: 0 auto; /* imprtant to logo margin */
                sheet-size: 300px 130mm; /* imprtant to set paper size */
            }

            html,body{margin:0;padding:0}
            #printContainer {
                width: 270px;
                margin: auto;
                /*padding: 10px;*/
                /*border: 2px dotted #000;              */
                text-align: justify;
            }
            table{
                width: 100%;
            }

            table td{
                width: 50%;
            }

            .text-center{text-align: center;}
        }
    </style>
</head>
<body>

<div id='printContainer'>

    <h2 style="padding-top:20px; margin-bottom: 0" class="text-center"><?= SystemSettings::getStoreName()?></h2>

    <table style="font-size: 10px; text-align: center">
        <tr>
            <td><?= SystemSettings::getAddress1()?></td>
        </tr>
        <tr>
            <td><?= SystemSettings::getAddress2()?></td>
        </tr>
        <tr>
            <td><?= SystemSettings::getContactNumber()?></td>
        </tr>
    </table>

    <hr>

    <?php
        $text = 'Expense('.$model->expenseType->expense_type_name. '), Invoice# '.$model->expense_id.' Amount#'.Utility::asCurrency($model->expense_amount).' Approved By: '.$model->approvedBy->username;
        $qrCode = new \Endroid\QrCode\QrCode($text);
        $qrCode->setSize(90);
        $qrCode->setPadding(0);
    ?>

    <p class="text-center" style="margin: 0; padding: 0"><img src="<?= $qrCode->getDataUri() ?>" alt="QR-code" class="left"/></p>

    <hr>

    <table style="font-size: 11px">

        <tr>
            <td>INVOICE</td>
            <td><b>#<?php echo $model->expense_id;?></b></td>
        </tr>

        <tr>
            <td>TYPE</td>
            <td><?php echo  $model->expenseType->expense_type_name;?>(<?php echo  $model->type;?>)</td>
        </tr>

        <tr>
            <td>PREPARE BY</td>
            <td> <?php echo $model->user->username?></td>
        </tr>

        <tr>
            <td>REMARKS</td>
            <td><?php echo  $model->expense_remarks;?></td>
        </tr>
        <tr>
            <td>DATE</td>
            <td><?php echo DateTimeUtility::getDate($model->created_at, SystemSettings::dateTimeFormat())?></td>
        </tr>

    </table>

    <hr>

    <table style="font-size: 12px">
        <tr>
            <td>AMOUNT</td>
            <td><b><?php echo Utility::asCurrency($model->expense_amount);?></b></td>
        </tr>
    </table>
    <hr>

    <table style="font-size: 10px; text-align: center">
        <tr>
            <td>PRINT <?php echo DateTimeUtility::getDate(null, 'd/m/Y h:i A');?></td>
        </tr>
    </table>

</div>
</body>
</html>
