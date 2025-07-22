<?php
/** @var string $clientName */
/** @var string $publicUrl */

use app\components\SystemSettings;

$logoBase64 = '';
$logoPath = Yii::getAlias('@webroot/images/logo.png');

if (file_exists($logoPath)) {
    $data = file_get_contents($logoPath);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($logoPath); // e.g., image/png
    $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($data);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice Sent</title>
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
                Thank you for shopping with <strong><?= SystemSettings::getStoreName()?></strong>.<br>
                Your invoice has been successfully generated and <strong>attached</strong> to this email as a PDF.
            </p>

            <p style="font-size: 15px; color: #444;">
                If you'd prefer, you can also view or download your invoice from the following link:
            </p>

            <p>
                <a href="<?= $publicUrl ?>" style="background-color: #1a73e8; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold;" target="_blank">
                    View Invoice Online
                </a>
            </p>

            <p style="margin-top: 30px; font-size: 14px; color: #666;">
                If you have any questions, feel free to contact us at:
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
