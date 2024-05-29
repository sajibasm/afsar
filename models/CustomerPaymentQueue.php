<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */

namespace app\models;

use app\components\SMS;
use app\components\TemplateDecode;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use yii\base\BaseObject;
use yii\helpers\Json;
use yii\queue\JobInterface;

class CustomerPaymentQueue extends BaseObject implements JobInterface
{

    public $paymentId;
    private $contact;
    private $amount;
    private $type='CASH';

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $payment = ClientPaymentHistory::findOne($this->paymentId);
        if($payment){
            $client =  $payment->customer;
            if(!empty($client->client_contact_number)){
                $this->contact =  $client->client_contact_number;
            }elseif(!empty($client->client_contact_person_number)){
                $this->contact =  $client->client_contact_person_number;
            }

            $this->amount = (int) $payment->received_amount;
            if(strtoupper($payment->paymentType->type)==='BANK'){
                $extra = Json::decode($payment->extra);
                $bank = Bank::findOne($extra['bank_id']);
                $this->type = "BANK($bank->bank_name)";
            }

            $phoneUtil = PhoneNumberUtil::getInstance();

            try {
                $swissNumberProto = $phoneUtil->parse($this->contact, "BD");
                if ($phoneUtil->isValidNumber($swissNumberProto)) {
                    $this->contact = $swissNumberProto->getCountryCode() . $swissNumberProto->getNationalNumber();
                    $tags = ['AMOUNT' => $this->amount, 'TYPE' => $this->type];
                    $template = Template::findOne(['name'=>'DUE_RECEIVED']);
                    $message = TemplateDecode::decodeByTags($template, $tags);
                    $sms = new SMS();
                    $sms->send($message, $this->contact);
                }

            } catch (NumberParseException $e) {
                var_dump($e);
            }


        }
    }
}