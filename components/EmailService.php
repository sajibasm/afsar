<?php

namespace app\components;

use Yii;

class EmailService
{
    /**
     * Sends a generic email with optional attachment and public link.
     *
     * @param string $customerEmail
     * @param string $htmlView path in `@app/mail/...`
     * @param array $viewData associative array passed to the view (e.g., clientName, publicUrl)
     * @param string|null $subject optional subject line
     * @param string|null $attachmentPath absolute path to a file to attach
     * @param string|null $attachmentName custom file name to show in email
     * @return array JSON response (success or error)
     */
    public function sendCustomerEmail(
        string $customerEmail,
        string $htmlView,
        array $viewData = [],
        ?string $subject = null,
        ?string $attachmentPath = null,
        ?string $attachmentName = null
    ): array {
        /** @var \app\components\JsonResponseComponent $response */
        $response = Yii::$app->json;

        if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            return $response->error('Customer email is invalid or missing.');
        }

        try {
            $message = Yii::$app->mailer->compose($htmlView, $viewData)
                ->setFrom([Yii::$app->params['adminEmail'] => SystemSettings::Company()])
                ->setTo($customerEmail)
                ->setSubject($subject ?? "Notification from " . SystemSettings::Company());

            if ($attachmentPath && file_exists($attachmentPath)) {
                $message->attach($attachmentPath, [
                    'fileName' => $attachmentName ?? basename($attachmentPath),
                    'contentType' => 'application/pdf'
                ]);
            }

            $message->send();

            return $response->success('Email sent successfully.', [
                'email' => $customerEmail,
                'attachment' => $attachmentName ?? basename($attachmentPath)
            ]);
        } catch (\Exception $e) {
            Yii::error("Email sending failed: " . $e->getMessage(), __METHOD__);
            return $response->error('Failed to send email.', ['error' => $e->getMessage()]);
        }
    }

}