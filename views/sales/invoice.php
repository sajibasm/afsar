<?php

/* @var $model app\models\Sales */
/* @var $salesDetails app\models\SalesDetails */
/* @var $qrCode string */

// Generate public Invoice Lookup URL
use app\components\DateTimeUtility;
use app\components\SystemSettings;
use yii\bootstrap\Html;
use yii\helpers\Url;

$encryptedId = \app\components\Utility::encrypt($model->sales_id);
$publicUrl = Url::to(['sales/invoice-lookup', 'id' => $encryptedId], true);
$qrCodeUrl = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=' . urlencode($publicUrl);

$dueDate = $model->payment_due_date;
$today = date('Y-m-d');
$statusText = '';
$statusColor = '';

if (!empty($dueDate)) {
    try {
        $due = new DateTime($dueDate);
        $now = new DateTime($today);
        $diff = $now->diff($due);
        $days = (int)$diff->format('%r%a');

        if ($days < 0) {
            $statusText = abs($days) . ' day(s) overdue';
            $statusColor = '#f1948a'; // soft red
        } elseif ($days > 0) {
            $statusText = $days . ' day(s) remaining';
            $statusColor = '#58d68d'; // soft green
        } else {
            $statusText = 'Due today';
            $statusColor = '#f5b041'; // orange
        }
    } catch (Exception $e) {
        $dueDate = null;
    }
}

?>

<!--For the Top Header Company Logo, Barcode, Address-->
<table width="100%" style="margin-bottom: 20px;">
    <tr>
        <!-- Left: Logo -->
        <td style="width: 30%; text-align: left;">
            <img src="<?= \app\components\ImageAssetService::getLogo(true) ?>" alt="Company Logo" height="70px">
        </td>

        <!-- Center: QR Code -->
        <td style="width: 40%; text-align: center;">
            <div style="display: inline-block;">
                <img src="<?= $qrCode ?>" alt="QR Code" style="height: 100px; margin-bottom: 5px;"><br>
                <span style="font-size: 10px; color: #555;">Scan to view invoice online</span>
            </div>
        </td>

        <!-- Right: Company Info -->
        <td style="width: 30%; text-align: right; font-size:12px;">
            <strong style="font-size: 13px;"><?= strtoupper(SystemSettings::getStoreName()) ?></strong><br>
            <?= SystemSettings::getAddress1() ?>,<br>
            <?= SystemSettings::getAddress2() ?><br>
            Contact Number: <?= SystemSettings::getContactNumber() ?><br>
            Email: <?= SystemSettings::getContactEmail() ?><br>
        </td>
    </tr>
</table>

<table width="100%" cellspacing="0" cellpadding="6" style="margin-bottom: 10px; border-collapse: collapse;">
    <tr  style="background-color: #f0f0f0; text-align: center">
        <td style=" text-align: center; font-size: 13px; white-space: nowrap; border: 1px solid #666;">
            <strong>Invoice Number: <?= $model->sales_id; ?></strong>
        </td>
    </tr>
</table>

<!--Customer Info , Shipping Info and Invoice Details-->
<table width="100%" style="font-size: 12px; border-collapse: collapse; border: 1px solid #000; margin-bottom: 20px;">
    <thead>
    <tr style="background-color: #f0f0f0;">
        <!-- Top Headers with internal borders only -->
        <th style="border-right: 1px solid #000; padding: 6px; text-align: center;">Customer</th>
        <th style="border-right: 1px solid #000; padding: 6px; text-align: center;">Shipping</th>
        <th style="padding: 6px; text-align: center;">Invoice</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <!-- Customer Info -->
        <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px;">
            <div><strong>Name:</strong> <?= htmlspecialchars($model->client_name) ?></div>
            <div><strong>Address:</strong>
                <?= htmlspecialchars($model->client->client_address1) ?>
                <?= !empty($model->client->clientCity->city_name) ? ', ' . htmlspecialchars($model->client->clientCity->city_name) : '' ?>
            </div>
            <div><strong>Phone:</strong> <?= htmlspecialchars($model->client->client_contact_number) ?></div>
        </td>

        <!-- Shipping Info -->
        <td style="border-top: 1px solid #000; border-right: 1px solid #000; padding: 8px;">
            <div><strong>Transport:</strong> <?= htmlspecialchars($model->transport_name) ?></div>
            <div><strong>Tracking:</strong> <?= htmlspecialchars($model->tracking_number) ?></div>
            <div><strong>Condition:</strong> <?= htmlspecialchars($model->payment_condition) ?></div>
        </td>

        <!-- Invoice Info -->
        <td style="border-top: 1px solid #000; padding: 8px;">
            <div><strong>Outlet:</strong> <?= $model->outlet->name ?></div>
            <div><strong>Contact Number:</strong> <?= $model->outlet->contactNumber?></div>
            <div><strong>Prepared By:</strong><?= htmlspecialchars($model->user->first_name . ' ' . $model->user->last_name) ?></div>
            <div><strong>Printed Date:</strong> <?= DateTimeUtility::getDate($model->created_at, 'd-m-Y H:i:s') ?></div>
            <div><strong>Printed By:</strong> <?= Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name ?></div>
        </td>
    </tr>
    </tbody>
</table>



<!--Product Details-->
<table width="100%" style="font-size:12px; border-collapse: collapse;">
    <thead>
    <tr style="background-color: #f0f0f0;">
        <th style="padding: 8px; border: 1px solid #000; text-align: center;"><b>SL#</b></th>
        <th style="padding: 8px; border-top: 1px solid #000; border-bottom: 1px solid #000; border-right: none; text-align: left;"><b>ITEM</b></th>
        <th style="padding: 8px; border-top: 1px solid #000; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: none; text-align: left;"><b>BRAND</b></th>
        <th style="padding: 8px; border-top: 1px solid #000; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: none; text-align: left;"><b>SIZE</b></th>
        <th style="padding: 8px; border-top: 1px solid #000; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: none; text-align: right;"><b>QUANTITY</b></th>
        <th style="padding: 8px; border-top: 1px solid #000; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: none; text-align: right;"><b>UNIT PRICE</b></th>
        <th style="padding: 8px; border: 1px solid #000; text-align: right;"><b>TOTAL</b></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($salesDetails as $index => $product): ?>
        <tr>
            <td style="padding: 6px; border: 1px solid #000; text-align: center;"><?= $index + 1 ?></td>
            <td style="padding: 6px; border-left: none; border-right: none; border-top: 1px solid #000; border-bottom: 1px solid #000; text-align: left;"><?= $product->item->item_name ?></td>
            <td style="padding: 6px; border-left: 1px solid #000; border-right: none; border-top: 1px solid #000; border-bottom: 1px solid #000; text-align: left;"><?= $product->brand->brand_name ?></td>
            <td style="padding: 6px; border-left: 1px solid #000; border-right: none; border-top: 1px solid #000; border-bottom: 1px solid #000; text-align: left;"><?= $product->size->size_name ?></td>
            <td style="padding: 6px; border-left: 1px solid #000; border-right: none; border-top: 1px solid #000; border-bottom: 1px solid #000; text-align: right;"><?= $product->quantity ?></td>
            <td style="padding: 6px; border-left: 1px solid #000; border-right: none; border-top: 1px solid #000; border-bottom: 1px solid #000; text-align: right;"><?= Yii::$app->formatter->asDecimal($product->sales_amount) ?></td>
            <td style="padding: 6px; border: 1px solid #000; text-align: right;"><?= Yii::$app->formatter->asDecimal($product->total_amount) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<!--Payment Details-->
<table width="100%" style="border-collapse: collapse; font-size:12px; margin-top: 20px;">
    <tr>
        <!-- Left: Due Summary -->
        <td style="width: 50%; vertical-align: top; padding-right: 15px;">
            <table width="100%" style="border-collapse: collapse; font-size:12px;">
                <tr>
                    <td colspan="2" style="padding: 6px 5px; font-weight: bold; border-bottom: 1px solid #555;">
                        Due Summary
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px;">Previous Dues</td>
                    <td style="padding: 5px; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($previousDues) ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px;">Current Dues</td>
                    <td style="padding: 5px; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($model->due_amount - $model->reconciliation_amount) ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px; font-weight: bold;">Total Dues</td>
                    <td style="padding: 5px; font-weight: bold; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($previousDues + ($model->due_amount - $model->reconciliation_amount)) ?>
                    </td>
                </tr>
            </table>
        </td>

        <!-- Right summary column -->
        <td style="width: 40%; vertical-align: top;">
            <table width="100%" style="border-collapse: collapse; font-size:12px;">
                <tr>
                    <td style="padding: 5px; font-weight: bold;">SubTotal</td>
                    <td style="padding: 5px; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($model->total_amount - ($model->vat_amount + $model->advance_income_tax_amount)) ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px;">Discount</td>
                    <td style="padding: 5px; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($model->discount_amount * -1) ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px;">VAT (<?= SystemSettings::getVAT() ?>%)</td>
                    <td style="padding: 5px; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($model->vat_amount) ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px;">AIT (<?= SystemSettings::getAIT() ?>%)</td>
                    <td style="padding: 5px; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($model->advance_income_tax_amount) ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px; border-top: 1px solid #555;"><b>Net Payable</b></td>
                    <td style="padding: 5px; border-top: 1px solid #555; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($model->total_amount - $model->discount_amount) ?>
                    </td>
                </tr>

                <?php $totalPaid = $model->paid_amount + $reconciliationAmount; ?>

                <tr>
                    <td style="padding: 5px; border-top: 1px solid #555;">Paid/Advance</td>
                    <td style="padding: 5px; border-top: 1px solid #555; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($totalPaid) ?>
                    </td>
                </tr>

                <?php if ($reconciliationAmount > 0): ?>
                    <tr>
                        <td style="padding: 5px;">Reconciliation</td>
                        <td style="padding: 5px; text-align: right;">
                            <?= Yii::$app->formatter->asDecimal($reconciliationAmount) ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td style="padding: 5px; border-top: 1px solid #555;"><b>Total Paid</b></td>
                    <td style="padding: 5px; border-top: 1px solid #555; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($totalPaid) ?>
                    </td>
                </tr>

                <tr>
                    <td style="padding: 5px; border-top: 1px solid #555;">Dues</td>
                    <td style="padding: 5px; border-top: 1px solid #555; text-align: right;">
                        <?= Yii::$app->formatter->asDecimal($model->due_amount - $model->reconciliation_amount) ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!--In Word -->
<table width="100%" cellspacing="0" cellpadding="6" style="margin-top: 10px; border-collapse: collapse;">
    <tr>
        <td style="font-size: 12px; white-space: nowrap; border: 1px solid #666;">
            <strong>Amount In Word:</strong> <?= \app\components\InvoiceGenerator::numberToTakaWords($model->total_amount - $model->discount_amount) ?>
        </td>
    </tr>
</table>

<!--In Payment Due Date -->
<table width="100%" cellspacing="0" cellpadding="6" style="margin-top: 15px; border-collapse: collapse; font-size: 12px; border: 1px solid #ccc; background-color: #f9f9f9;">
    <tr>
        <td style="padding: 10px; border: 1px solid #ccc; text-align: left;">
            <strong style="color: #333;">Payment Due Date:</strong>
            <span style="margin-left: 8px; color: #000;">
                <?php if (!empty($dueDate)): ?>
                    <?= DateTimeUtility::getDate($dueDate, SystemSettings::getDateFormat()) ?>
                    <span style="margin-left: 10px; color: <?= $statusColor ?>; font-weight: bold;">(<?= Html::encode($statusText) ?>)</span>
                <?php else: ?>
                    <em style="color: #888;">N/A</em>
                <?php endif; ?>
            </span>
        </td>
    </tr>
</table>

<!--Signature Part-->
<div style="page-break-inside: avoid; margin-top: 50px;">
    <table width="100%" cellspacing="0" cellpadding="8" style="margin-top: 40px; border-collapse: collapse; font-size: 12px;">
        <tr>
            <td style="text-align: center;">
                &nbsp;
            </td>
            <td style="text-align: center;">
                <div>
                    <?= htmlspecialchars($model->user->first_name . ' ' . $model->user->last_name) ?>
                </div>
            </td>
            <td style="text-align: center;">
                <?= htmlspecialchars($model->authorized->first_name . ' ' . $model->authorized->last_name) ?>
            </td>
            <td style="text-align: center;">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td style="text-align: center; border-top: 1px solid #666;">
                <strong>Customer Signature</strong>
            </td>
            <td style="text-align: center; border-top: 1px solid #666;">
                <strong>Prepared By</strong>
            </td>
            <td style="text-align: center; border-top: 1px solid #666;">
                <strong>Approved By</strong>
            </td>
            <td style="text-align: center; border-top: 1px solid #666;">
                <strong>Authorized Signature</strong>
            </td>

        </tr>
    </table>
</div>



<!-- Notes Section -->
<table width="100%" cellspacing="0" cellpadding="6" style="margin-top: 20px; border-collapse: collapse; font-size: 12px;">
    <tr>
        <td style="border: 1px solid #666; vertical-align: top; height: 80px;">
            <?php echo SystemSettings::invoiceFooterMassage() ?>
        </td>
    </tr>
</table>