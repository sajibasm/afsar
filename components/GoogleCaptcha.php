<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;

use Yii;
use yii\httpclient\Client;
class GoogleCaptcha
{
    public static function validation($gRecaptchaResponse, $secret)
    {
        if (empty($gRecaptchaResponse)) {
            Yii::$app->session->setFlash('error', 'reCAPTCHA verification failed, please try again.');
            return false;
        } else {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://www.google.com/recaptcha/api/siteverify')
                ->setData(['secret' => $secret, 'response' => $gRecaptchaResponse])
                ->send();

            if ($response->isOk) {
                $responseData = $response->data;
                if (isset($responseData['success']) && $responseData['success']) {
                    return true;
                } else {
                    Yii::$app->session->setFlash('error', 'reCAPTCHA verification failed, please try again.');
                    return false;
                }
            } else {
                Yii::$app->session->setFlash('error', 'reCAPTCHA request failed, please try again.');
                return false;
            }
        }
    }
}