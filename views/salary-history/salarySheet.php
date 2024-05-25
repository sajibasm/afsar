<?php

use app\components\SystemSettings;
use app\components\CommonUtility;
use app\components\DateTimeUtility;
use dosamigos\qrcode\QrCode;
use yii\helpers\Url;


$totalAdvance = 0;
$totalAvailable = 0;
$totalAmount = 0;

/* @var $model app\models\Sales */
/* @var $salesDetails app\models\SalesDetails */
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

    <div style="width: 100%">

    <div id="logo" style="width: 20%; float:left;">
        <img height="70px" src="<?= Url::base(true) . '/images/'.SystemSettings::getLogo(); ?>">
    </div>

    <div class="barcode" style="width: 50%; float:left; margin-left: 5%;">
        <?php
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        //echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($model->sales_id, $generator::TYPE_CODE_128)) . '">';
        ?>
        <div style="border: 1px solid #DDD; text-align: center; margin: 0 22%;">
            <p style="border-bottom: 1px solid #DDD; padding: 0; font-size: 14px; line-height: 28px; font-weight: bold">SALARY SHEET</p>
            <p style="font-size: 12px; color: #777; margin-bottom: 1px;"><?= CommonUtility::getMonthName($month) ?>, <?= $year ?></p>
        </div>
    </div>

    <div id="company" style="width: 25%; float:left;">
        <h2 class="name"><?= SystemSettings::getStoreName()?></h2>
        <div><?= SystemSettings::getAddress1()?></div>
        <div><?= SystemSettings::getAddress2()?></div>
        <div><a href="#"><?= SystemSettings::getContactNumber()?></a></div>
    </div>

    </div>

</header>

<main>

    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="no">#</th>
            <th class="item">Name</th>
            <th class="qty">Salary</th>
            <th class="qty">Advance/Paid</th>
            <th class="qty">Remaining</th>
            <th class="total" style="text-align: center">Sign</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($salaries as $index => $salary): ?>
        <tr>
            <td class="no"><?= $index + 1 ?></td>
            <td class="item"><?= strtoupper($salary['name'] )?></td>
            <td class="qty"><?= number_format($salary['salary'], 2) ?> BDT</td>
            <td class="qty"><?= number_format($salary['withdraw'], 2) ?> BDT</td>
            <td class="qty"><?= number_format($salary['remaining'], 2) ?> BDT</td>
            <td class="qty" style="width: 200px"></td>
        </tr>

        <?php
            $totalAmount+=$salary['salary'];
            $totalAdvance+=$salary['withdraw'];
            $totalAvailable+=$salary['remaining'];
        ?>

        <?php endforeach; ?>
        </tbody>

        <tr>
            <td class="item" colspan="2" style="text-align: right"><b>TOTAL</b></td>
            <td class="qty"><?= number_format($totalAmount, 2); ?> BDT</td>
            <td class="qty"><?= number_format($totalAdvance, 2); ?> BDT</td>
            <td class="qty"><?= number_format($totalAvailable, 2); ?> BDT</td>
            <td class="qty"></td>
        </tr>

    </table>

    <table id="sign" style="" border="0" cellspacing="0" cellpadding="0">
            <tr>

                <td style="text-align: center;">

                </td>

                <td>

                </td>
            </tr>

            <tr style="border-bottom: none;">
                <td style="text-align: center;">
                    Prepared by
                </td>
                <td style="text-align: right;">
                    Authorized signature
                </td>
            </tr>
        </table>

</main>

</body>
</html>