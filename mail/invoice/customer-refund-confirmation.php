<?php
/** @var string $clientName */

use app\components\ImageAssetService;use app\components\SystemSettings;
$logoBase64 = ImageAssetService::getLogo(true);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Refund Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 30px;">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; margin: auto; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
    <tr>
        <td style="padding: 30px; text-align: center;">
            <?php if ($logoBase64): ?>
                <img src="<?= $logoBase64 ?>" alt="<?= SystemSettings::Company()?> Logo" style="height: 60px; margin-bottom: 25px;">
            <?php else: ?>
                <p style="color: red;">[Logo not found or could not be loaded]</p>
            <?php endif; ?>

            <p style="font-size: 16px;">Dear <?= htmlspecialchars($clientName) ?>,</p>

            <p style="font-size: 15px; color: #444;">
                We would like to inform you that a refund has been successfully processed by <strong><?= SystemSettings::Company()?></strong>.
            </p>

            <p style="font-size: 15px; color: #444;">
                A refund receipt has been generated and <strong>attached</strong> to this email in PDF format for your records.
            </p>

            <p style="margin-top: 30px; font-size: 14px; color: #666;">
                If you have any questions or need further assistance, please contact us at:
                <a href="mailto:<?= SystemSettings::CompanyEmail()?>"><?= SystemSettings::CompanyEmail()?></a>
            </p>

            <p style="font-size: 13px; color: #999;">
                &copy; <?= date('Y') ?> <a href="<?= SystemSettings::CompanyDomain()?>" style="color: #999; text-decoration: none;"><?= SystemSettings::CompanyDomain()?></a> — All rights reserved.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
