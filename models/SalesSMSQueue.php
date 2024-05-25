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
use app\models\Client;
use app\models\Sales;
use app\models\Template;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SalesSMSQueue extends  BaseObject implements JobInterface
{
    const SALES_SMS = 'SALES_SMS';
    const SALES_TRANSPORT_SMS = 'SALES_TRANSPORT_SMS';

    public $salesId;
    private $contact;
    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $sales  =  Sales::findOne($this->salesId);
        if(!empty($sales->client_mobile)){
            $this->contact = $sales->client_mobile;
        }elseif(!empty($sales->contact_number)){
            $this->contact = $sales->contact_number;
        } else{
            $client  = Client::findOne($sales->client_id);
            if(!empty($client->client_contact_number)){
                $this->contact =  $client->client_contact_number;
            }else if(!empty($client->client_contact_person_number)){
                $this->contact =  $client->client_contact_person_number;
            }
        }


        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $swissNumberProto = $phoneUtil->parse($this->contact, "BD");
            if($phoneUtil->isValidNumber($swissNumberProto)){
                $this->contact = $swissNumberProto->getCountryCode().$swissNumberProto->getNationalNumber();
                $template = Template::findOne(['name'=>!empty($sales->transport_id)?self::SALES_TRANSPORT_SMS:self::SALES_SMS]);
                if(!empty($sales->transport_id)){
                    $tags = ['AMOUNT'=>$sales->total_amount, 'TRANSPORT'=>$sales->transport->transport_name, 'TRACKING'=>$sales->tracking_number,];
                }else{
                    $tags = ['AMOUNT'=>$sales->total_amount, 'INVOICE'=>$sales->sales_id,];
                }
                $message = TemplateDecode::decodeByTags($template, $tags);
                $sms = new SMS();
                $sms->send($message, $this->contact);
            }

        } catch (NumberParseException $e) {
            var_dump($e);
        }

    }
}