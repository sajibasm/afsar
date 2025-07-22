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
use yii\helpers\Json;
use yii\helpers\Url;

$encryptedId = \app\components\Utility::encrypt($model->sales_id);
$publicUrl = Url::to(['sales/invoice-lookup', 'id' => $encryptedId], true);
$qrCodeUrl = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=' . urlencode($publicUrl);
?>

<div style="font-size: 12px;">

    <!-- Header: Logo and Company Info -->
    <table width="100%" style="margin-bottom: 20px; font-size: 12px;">
        <tr>
            <td style="width: 50%; text-align: left; font-size: 12px;">
                <img src="<?= \app\components\ImageAssetService::getLogo(true) ?>" alt="Company Logo" height="70px">
            </td>
            <td style="width: 50%; text-align: right; font-size: 12px;">
                <strong style="font-size: 13px;"><?= strtoupper(SystemSettings::getStoreName()) ?></strong><br>
                <div style="font-size: 12px;"><?= SystemSettings::getAddress1() ?>,</div>
                <div style="font-size: 12px;"><?= SystemSettings::getAddress2() ?></div>
                <div style="font-size: 12px;">Contact Number: <?= SystemSettings::getContactNumber() ?></div>
                <div style="font-size: 12px;">Email: <?= SystemSettings::getContactEmail() ?></div>
            </td>
        </tr>
    </table>

    <!-- Invoice Number -->
    <table width="100%" cellspacing="0" cellpadding="6" style="margin-bottom: 10px; border-collapse: collapse; font-size: 12px;">
        <tr style="background-color: #f0f0f0; text-align: center;">
            <td style="text-align: center; white-space: nowrap; border: 1px solid #666; font-size: 12px;">
                <strong style="font-size: 13px;">Payment Invoice Number: <?= $model->client_payment_history_id; ?></strong>
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
            <div><strong>Printed Date:</strong> <?= DateTimeUtility::getTime($model->received_at, SystemSettings::getDateFormat()) ?></div>
            <div><strong>Printed By:</strong> <?= Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name ?></div>
        </td>
        </tbody>
    </table>

    <!-- Payment Adjustment Details -->
    <table width="100%" style="border-collapse: collapse; border: 1px solid #000; margin-bottom: 20px; font-size: 12px;">
        <thead>
        <tr style="background-color: #f0f0f0;">
            <th colspan="2" style="padding: 6px; text-align: center; font-size: 12px;">Received Payment</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
                <strong style="font-size: 12px;">Payment ID</strong>
            </td>
            <td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">
                <?= $model->client_payment_history_id ?>
            </td>
        </tr>
        <tr>
            <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
                <strong style="font-size: 12px;">Received Date</strong>
            </td>
            <td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">
                <?= DateTimeUtility::getDate($model->received_at, SystemSettings::getDateFormat()) ?>
            </td>
        </tr>
        <tr>
            <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
                <strong style="font-size: 12px;">Type</strong>
            </td>
            <td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">
                <?= htmlspecialchars($model->received_type) ?>
            </td>
        </tr>


        <tr>
            <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
                <strong style="font-size: 12px;">Payment Method</strong>
            </td>
            <td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">
                <?php
                if ($model->received_type == ClientPaymentHistory::RECEIVED_TYPE_SALES_RETURN) {
                    echo strtoupper('N/A');
                } elseif ($model->paymentType->payment_type_name == PaymentType::TYPE_CASH) {
                    echo strtoupper(PaymentType::TYPE_CASH);
                } else {
                    $object = Json::decode($model->extra);
                    $bank = Bank::findOne($object['bank_id']);
                    $branch = Branch::findOne($object['branch_id']);
                    echo PaymentType::TYPE_DEPOSIT . " (" . $bank->bank_name . ", " . $branch->branch_name . ")";
                }
                ?>
            </td>
        </tr>
        <?php
        $rows = [
            ['Purpose', strtoupper($model->received_type)],
            ['Remarks', htmlspecialchars($model->remarks)],
            ['Received Amount', Yii::$app->formatter->asDecimal($model->received_amount) . ' ' . SystemSettings::getAppCurrency()],
            ['Available Amount', Yii::$app->formatter->asDecimal($model->remaining_amount) . ' ' . SystemSettings::getAppCurrency()],
        ];
        foreach ($rows as [$label, $value]) {
            echo '<tr>';
            echo '<td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;"><strong style="font-size: 12px;">' . $label . '</strong></td>';
            echo '<td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">' . $value . '</td>';
            echo '</tr>';
        }
        ?>


        <tr>
            <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 12px;">
                <strong style="font-size: 12px;">Amount In Word:</strong>
            </td>
            <td style="border-top: 1px solid #000; padding: 8px; font-size: 12px;">
                <?= \app\components\InvoiceGenerator::numberToTakaWords($model->received_amount) ?>
            </td>
        </tr>
        </tbody>
    </table>

=

    <!-- Settlement Invoice -->
    <table width="100%" style="border-collapse: collapse; margin-bottom: 20px; font-size: 12px;">
        <thead>
        <tr>
            <th colspan="5" style="padding: 10px; border: 1px solid #000; background-color: #e0e0e0; text-align: center; font-size: 12px;">
                Invoice Settlement
            </th>
        </tr>
        <tr style="background-color: #f0f0f0; font-size: 12px;">
            <th style="padding: 8px; border: 1px solid #000; text-align: center;">SL#</th>
            <th style="padding: 8px; border: 1px solid #000; text-align: left;">Datetime</th>
            <th style="padding: 8px; border: 1px solid #000; text-align: center;">Invoice</th>
            <th style="padding: 8px; border: 1px solid #000; text-align: left;">Type</th>
            <th style="padding: 8px; border: 1px solid #000; text-align: right;">Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php $invoiceTotal = 0; ?>
        <?php foreach ($details as $index => $detail): ?>
            <tr style="font-size: 12px;">
                <td style="padding: 6px; border: 1px solid #000; text-align: center;"><?= $index + 1 ?></td>
                <td style="padding: 6px; border: 1px solid #000;"><?= DateTimeUtility::getTime($detail->created_at, SystemSettings::getDateFormat()) ?></td>
                <td style="padding: 6px; border: 1px solid #000; text-align: center;"><?= $detail->sales_id ?></td>
                <td style="padding: 6px; border: 1px solid #000;"><?= strtoupper($detail->payment_type) ?></td>
                <td style="padding: 6px; border: 1px solid #000; text-align: right;"><?= Yii::$app->formatter->asDecimal($detail->paid_amount) ?></td>
            </tr>
            <?php $invoiceTotal += $detail->paid_amount; ?>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr style="font-size: 12px;">
            <td colspan="4" style="padding: 8px; border: 1px solid #000; text-align: right;"><strong style="font-size: 12px;">Total</strong></td>
            <td style="padding: 8px; border: 1px solid #000; text-align: right;"><strong style="font-size: 12px;"><?= Yii::$app->formatter->asDecimal($invoiceTotal) ?></strong></td>
        </tr>
        </tfoot>
    </table>

    <!-- Signature Section -->
    <div style="page-break-inside: avoid; margin-top: 50px; font-size: 12px;">
        <table width="100%" cellspacing="0" cellpadding="8" style="margin-top: 40px; border-collapse: collapse; font-size: 12px;">
            <tr>
                <td style="text-align: center; font-size: 12px;">&nbsp;</td>
                <td style="text-align: center; font-size: 12px;"><?= htmlspecialchars($model->user->first_name . ' ' . $model->user->last_name) ?></td>
                <td style="text-align: center; font-size: 12px;"><?= htmlspecialchars($model->approvedBy->first_name . ' ' . $model->approvedBy->last_name) ?></td>
                <td style="text-align: center; font-size: 12px;">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: center; border-top: 1px solid #666; font-size: 12px;"><strong style="font-size: 12px;">Customer Signature</strong></td>
                <td style="text-align: center; border-top: 1px solid #666; font-size: 12px;"><strong style="font-size: 12px;">Received By</strong></td>
                <td style="text-align: center; border-top: 1px solid #666; font-size: 12px;"><strong style="font-size: 12px;">Approved By</strong></td>
                <td style="text-align: center; border-top: 1px solid #666; font-size: 12px;"><strong style="font-size: 12px;">Authorized Signature</strong></td>
            </tr>
        </table>
    </div>

</div>
