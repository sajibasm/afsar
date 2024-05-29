<?php


namespace app\components;

use app\models\SmsGateway;
use GuzzleHttp\Client;
use yii\helpers\Json;
use Yii;


class SMS
{
    private $url;
    private $apiKey;
    private $senderId;

    public function __construct()
    {
        $sms = SmsGateway::find()->one();
        if($sms){
            $this->apiKey = trim($sms->apiKey);
            $this->url = trim($sms->url);
            $this->senderId = trim($sms->senderId);
        }
    }

    private function getUrl()
    {
        $pos = strpos($this->url, '/');
        if ($pos === false) { $this->url.="/";}
        return trim($this->url."smsapi");
    }

    public function send($message, $contact, $type='text')
    {
        $client = new Client(['verify' => false, 'headers' => [ 'Content-Type' => 'application/json' ]]);

        $data = [
            "api_key" => $this->apiKey,
            "type" => $type,
            "contacts" =>   $contact,
            "senderid" => $this->senderId,
            "msg" => $message,
        ];

        $result = $client->post($this->getUrl(), ['json' => $data]);
        echo($result->getBody());

    }
}