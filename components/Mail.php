<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;

use app\models\Client;
use app\models\ClientPaymentHistory;
use app\models\EmailQueue;
use app\models\Sales;
use app\models\Template;
use yii\helpers\ArrayHelper;

class Mail
{
    public static function getInvoiceTags($string)
    {
        $tags = [];
        $array = explode(" ", $string);
        foreach ($array as $key){
            $findme   = '{';
            $start = strpos($key, $findme);
            if ($start !== false) {
                $findme   = '}';
                $end = strpos($key, $findme);
                if ($end !== false) {
                    $substring = substr($key, $start, $end);
                    $newString = str_replace('{', '', $substring);
                    $newString = str_replace('}', '', $newString);
                    $tags[$newString] = "{".$newString."}";
                }
            }
        }
        return $tags;
    }

    public static function sendInvoice($invoiceId)
    {
        $tags = [];
        $subject = null;
        $body = null;
        $sales = Sales::findOne($invoiceId);

        $template = Template::find()->where(['name'=>EmailQueue::TEMPLATE_INVOICE])->one();
        $tags = self::getInvoiceTags( $template->subject );
        $subject = $template->subject;
        foreach ($tags as $key=>$value){
            $subject  = str_replace($value, $sales->{$key}, $subject);
        }

        $body = $template->body;
        $tags = self::getInvoiceTags( $template->body );

        foreach ($tags as $key=>$value){
            $body  = str_replace($value, $sales->{$key}, $body);
        }

        $customer = Client::findOne($sales->client_id);

        if(!empty($customer->email)){
            $file = PdfGen::salesInvoice($invoiceId, true);;
            $message = \Yii::$app->mail->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => 'Axial Solution Ltd'])
                ->setTo($customer->email)
                ->attach($file)
                ->setSubject( $subject )
                ->setHtmlBody($body)
                ->send();

            if($message){
                return true;
            }

            return false;
        }

        return false;

    }

    public static function sendPaymentReceipt($receiptId)
    {
        $tags = [];
        $subject = null;
        $body = null;
        $model = ClientPaymentHistory::findOne($receiptId);

        $template = Template::find()->where(['name'=>EmailQueue::TEMPLATE_PAYMENT_RECEIPT])->one();
        $tags = self::getInvoiceTags( $template->subject );
        $subject = $template->subject;
        foreach ($tags as $key=>$value){

            if($key=='payment_type'){
                $key = 'paymentType';
                $subject  = str_replace($value, $model->{$key}->payment_type_name, $subject);
            }else{
                $subject  = str_replace($value, $model->{$key}, $subject);
            }

        }


        $body = $template->body;
        $tags = self::getInvoiceTags( $template->body );

        foreach ($tags as $key=>$value){

            if($key=='client_name'){
                $key='customer';
                $body  = str_replace($value, $model->{$key}->client_name, $body);
            }else if($key=='payment_type'){
                $key = 'paymentType';
                $body = str_replace($value, $model->{$key}->payment_type_name, $body);
            }else{
                $body  = str_replace($value, $model->{$key}, $body);
            }
        }


        $customer = Client::findOne($model->client_id);

        if(!empty($customer->email)){
            $file = PdfGen::paymentReceipt($receiptId, true, $controller);;
            $message = \Yii::$app->mail->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => 'Axial Solution Ltd'])
                ->setTo($customer->email)
                ->attach($file)
                ->setSubject( $subject )
                ->setHtmlBody($body)
                ->send();

            if($message){
                return true;
            }

            return false;
        }

        return false;

    }


}