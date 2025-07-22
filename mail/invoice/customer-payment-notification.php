<?php
/** @var string $clientName */

use app\components\SystemSettings;

$logoBase64 = \app\components\ImageAssetService::getLogo(true);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Received</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 30px;">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; margin: auto; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
    <tr>
        <td style="padding: 30px; text-align: center;">
            <?php if ($logoBase64): ?>
                <img src="<?= $logoBase64 ?>" alt="<?= SystemSettings::getStoreName()?> Logo" style="height: 60px; margin-bottom: 25px;">
            <?php else: ?>
                <p style="color: red;">[Logo not found or could not be loaded]</p>
            <?php endif; ?>

            <p style="font-size: 16px;">Dear <?= htmlspecialchars($clientName) ?>,</p>

            <p style="font-size: 15px; color: #444;">
                We have successfully received your payment at <strong><?= SystemSettings::getStoreName()?></strong>.
            </p>

            <p style="font-size: 15px; color: #444;">
                A confirmation receipt has been generated and <strong>attached</strong> to this email as a PDF for your records.
            </p>

            <p style="margin-top: 30px; font-size: 14px; color: #666;">
                If you have any questions or concerns, please don’t hesitate to contact us at:
                <a href="mailto:<?= SystemSettings::getContactEmail()?>"><?= SystemSettings::getContactEmail()?></a>
            </p>

            <p style="font-size: 13px; color: #999;">
                &copy; <?= date('Y') ?> <a href="<?= SystemSettings::getDomain()?>" style="color: #999; text-decoration: none;"><?= SystemSettings::getDomain()?></a> — All rights reserved.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
