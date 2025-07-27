<?php

/* @var $model app\models\ClientPaymentHistory */
/* @var $salesDetails app\models\SalesDetails */
/* @var $qrCode string */

use app\components\DateTimeUtility;
use app\components\SystemSettings;
use app\models\Bank;
use app\models\Branch;
use app\models\ClientPaymentHistory;
use app\models\PaymentType;
use Picqer\Barcode\BarcodeGeneratorPNG;
use yii\helpers\Json;
use yii\helpers\Url;

$generator = new BarcodeGeneratorPNG();
$barcodeText = SystemSettings::CompanyShortName().'-CPR '.$withdraw->id;
$barcodeImage = base64_encode($generator->getBarcode($barcodeText, $generator::TYPE_CODE_128));
$barcode = 'data:image/png;base64,' . $barcodeImage;
?>

<div style="font-size: 12px;">

    <!-- Header: Logo and Company Info -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
        <tr style="vertical-align: middle;">
            <!-- Logo -->
            <td style="width: 30%; text-align: left;">
                <img src="<?= \app\components\ImageAssetService::getLogo(true) ?>" alt="Company Logo" height="70px">
            </td>

            <!-- Barcode -->
            <td style="width: 30%; text-align: center;">
                <img src="<?= $barcode ?>" alt="Barcode" style="height: 45px;"><br>
                <span style="font-size: 9px; color: #555;"><?= $barcodeText ?></span>
            </td>

            <!-- Company Info -->
            <td style="width: 40%; text-align: right; font-size: 13px;">
                <strong style="font-size: 18px;"><?= SystemSettings::Company() ?></strong><br>

                <strong>Address:</strong> <?= SystemSettings::CompanyAddress1() ?><br>
                <?php if(SystemSettings::CompanyAddress2()): ?>
                    <strong>Address2:</strong> <?= SystemSettings::CompanyAddress2()?><br>
                <?php endif; ?>

                <?php if(SystemSettings::CompanyCity()): ?>
                    <strong>City:</strong> <?= SystemSettings::CompanyCity()?><br>
                <?php endif; ?>
                <?php if(SystemSettings::CompanyPostalCode()): ?>
                    <strong>Postal Code:</strong> <?= SystemSettings::CompanyPostalCode()?><br>
                <?php endif; ?>

                <strong>Contact Number:</strong> <?= SystemSettings::CompanyContactNumber() ?><br>
                <strong>Phone Number:</strong> <?= SystemSettings::CompanyPhoneNumber() ?><br>
                <strong>Email:</strong> <?= SystemSettings::CompanyEmail() ?><br>
                <strong>Website:</strong> <?= SystemSettings::CompanyDomain() ?>
            </td>
        </tr>
    </table>

    <!-- Invoice Number -->
    <table width="100%" cellspacing="0" cellpadding="6" style="margin-bottom: 10px; border-collapse: collapse; font-size: 12px;">
        <tr style="background-color: #f0f0f0; text-align: center;">
            <td style="text-align: center; white-space: nowrap; border: 1px solid #666; font-size: 12px;">
                <strong style="font-size: 13px;">Refund Receipt Number: <?= $withdraw->id; ?></strong>
            </td>
        </tr>
    </table>

    <!-- Customer and Transaction Info -->
    <table width="100%" style="border-collapse: collapse; border: 1px solid #000; margin-bottom: 20px; font-size: 12px;">
        <thead>
        <tr style="background-color: #f0f0f0;">
            <th style="border-right: 1px solid #000; padding: 6px; text-align: center; font-size: 12px;">Customer</th>
            <th style="padding: 6px; text-align: center;">Store</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
                <div style="font-size: 12px;"><strong style="font-size: 12px;">Name:</strong> <?= htmlspecialchars($model->customer->client_name) ?></div>
                <div style="font-size: 12px;"><strong style="font-size: 12px;">Address:</strong>
                    <?= htmlspecialchars($model->customer->client_address1) ?>
                    <?php if (!empty($model->customer->clientCity->city_name)): ?>
                        , <?= htmlspecialchars($model->customer->clientCity->city_name) ?>
                    <?php endif; ?>
                </div>
                <div style="font-size: 12px;"><strong style="font-size: 12px;">Phone:</strong> <?= htmlspecialchars($model->customer->client_contact_number) ?></div>
            </td>
        </tr>
        <td style="border-top: 1px solid #000; padding: 8px;">
            <div><strong>Outlet:</strong> <?= $model->outletDetail->name ?></div>
            <div><strong>Contact Number:</strong> <?= $model->outletDetail->contactNumber ?></div>
            <div><strong>Printed Date:</strong> <?= DateTimeUtility::getTime($model->received_at, SystemSettings::DateFormat()) ?></div>
            <div><strong>Printed By:</strong> <?= Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name ?></div>
        </td>
        </tbody>
    </table>

    <!-- Payment Adjustment Details -->
    <table width="100%" style="border-collapse: collapse; border: 1px solid #000; margin-bottom: 20px; font-size: 12px;">
        <thead>
        <tr style="background-color: #f0f0f0;">
            <th colspan="2" style="padding: 6px; text-align: center; font-size: 12px;">Payment Refund Info</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
                <strong style="font-size: 12px;">Type</strong>
            </td>
            <td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">
                <?= strtoupper($model->received_type) ?>
            </td>
        </tr>

        <tr>
        <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
            <strong style="font-size: 12px;">Approved Type</strong>
        </td>
        <td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">
            <?= strtoupper($model->approved_received_type) ?>
        </td>
        </tr>

        <tr>
            <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
                <strong style="font-size: 12px;">Remarks</strong>
            </td>
            <td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">
                <?= $model->remarks ?>
            </td>
        </tr>
        <tr>
            <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
                <strong style="font-size: 12px;">Amount</strong>
            </td>
            <td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">
                <?= Yii::$app->formatter->asDecimal($withdraw->amount)?>
            </td>
        </tr>



        <tr>
            <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
                <strong style="font-size: 12px;">Amount In Word:</strong>
            </td>
            <td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">
                <?= \app\components\PdfGenerator::numberToTakaWords($withdraw->amount) ?>
            </td>
        </tr>
        </tbody>
    </table>


    <!-- Signature Section -->
    <div style="page-break-inside: avoid; margin-top: 50px; font-size: 12px;">
        <table width="100%" cellspacing="0" cellpadding="8" style="margin-top: 40px; border-collapse: collapse; font-size: 12px;">
            <tr>
                <td style="text-align: center; font-size: 12px;">&nbsp;</td>
                <td style="text-align: center; font-size: 12px;"><?= htmlspecialchars($withdraw->user->first_name . ' ' . $withdraw->user->last_name) ?></td>
                <td style="text-align: center; font-size: 12px;"><?= htmlspecialchars($withdraw->updatedUser->first_name . ' ' . $withdraw->updatedUser->last_name) ?></td>
                <td style="text-align: center; font-size: 12px;">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: center; border-top: 1px solid #666; font-size: 12px;"><strong style="font-size: 12px;">Customer Signature</strong></td>
                <td style="text-align: center; border-top: 1px solid #666; font-size: 12px;"><strong style="font-size: 12px;">Created By</strong></td>
                <td style="text-align: center; border-top: 1px solid #666; font-size: 12px;"><strong style="font-size: 12px;">Approved By</strong></td>
                <td style="text-align: center; border-top: 1px solid #666; font-size: 12px;"><strong style="font-size: 12px;">Authorized Signature</strong></td>
            </tr>
        </table>
    </div>

</div>
