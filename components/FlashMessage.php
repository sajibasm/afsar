<?php

namespace app\components;

use Yii;

class FlashMessage
{
    /**
     * Set a flash message for SweetAlert2 display.
     *
     * @param string $message The body text of the alert
     * @param string $title   The title of the alert
     * @param string $type    Alert type: 'success', 'error', 'info', 'warning', 'question'
     * @param int|null $timer Optional auto-close timer (ms), null for no timer
     * @param bool $showConfirmButton Show confirm button (default: true)
     */
    public static function setMessage(
        string $message,
        string $title = 'Notice',
        string $type = 'info',
        ?int $timer = 5000,
        bool $showConfirmButton = true
    ): void {
        Yii::$app->session->setFlash('swal', [
            'title' => $title,
            'text' => $message,
            'type' => $type,
            'timer' => $timer,
            'showConfirmButton' => $showConfirmButton,
        ]);
    }


    /**
     * Render SweetAlert2 message script if set.
     *
     * @return string
     */
    public static function getMessage(): string
    {
        $swal = Yii::$app->session->getFlash('swal');
        if (!$swal) {
            return '';
        }

        $title = json_encode($swal['title'] ?? 'Notice');
        $text = json_encode($swal['text'] ?? '');
        $type = json_encode($swal['type'] ?? 'info');
        $timer = isset($swal['timer']) ? (int)$swal['timer'] : 'null';
        $showConfirmButton = isset($swal['showConfirmButton']) ? ($swal['showConfirmButton'] ? 'true' : 'false') : 'true';

        return <<<HTML
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        title: $title,
        text: $text,
        icon: $type,
        confirmButtonText: 'OK',
        timer: $timer,
        showConfirmButton: $showConfirmButton
    });
});
</script>
HTML;
    }

}