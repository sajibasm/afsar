<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%client}}".
 *client_name
 * @property integer $client_id
 * @property integer $outletId
 * @property string $client_name
 * @property integer $client_city
 * @property string $client_address1
 * @property string $client_address2
 * @property string $client_contact_number
 * @property string $client_contact_person
 * @property string $client_contact_person_number
 * @property string $email
 * @property string $client_type
 * @property double $client_balance
 *
 * @property Challan[] $challans
 * @property City $clientCity
 * @property ClientPaymentDetails[] $clientPaymentDetails
 * @property ClientPaymentHistory[] $clientPaymentHistories
 * @property ClientSalesPayment[] $clientSalesPayments
 * @property MarketBookSalesDetails[] $marketBookSalesDetails
 * @property Sales[] $sales
 */
class Client extends \yii\db\ActiveRecord
{

    const CUSTOMER_TYPE_REGULAR = 'regular';
    const CUSTOMER_TYPE_IRREGULAR = 'irregular';

    public $city;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_name','client_city','client_address1', 'client_contact_number' ,'client_type'], 'required'],
            [['client_city', 'outletId'], 'integer'],
            [['client_type', 'city'], 'string'],
            [['client_balance'], 'number'],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 100],
            [['client_name'], 'string', 'max' => 100],
            [['client_address1', 'client_address2', 'client_contact_person'], 'string', 'max' => 50],
            [['client_contact_number', 'client_contact_person_number'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'city' => Yii::t('app', 'City'),
            'client_id' => Yii::t('app', 'ID'),
            'outletId' => Yii::t('app', 'Outlet'),
            'client_name' => Yii::t('app', 'Full Name'),
            'client_city' => Yii::t('app', 'City'),
            'client_address1' => Yii::t('app', 'Address1'),
            'client_address2' => Yii::t('app', 'Address2'),
            'client_contact_number' => Yii::t('app', 'Contact Number'),
            'client_contact_person' => Yii::t('app', 'Contact Person'),
            'client_contact_person_number' => Yii::t('app', 'Contact Person Number'),
            'client_type' => Yii::t('app', 'Type'),
            'Email' => Yii::t('app', 'Client Type'),
            'client_balance' => Yii::t('app', 'Client Balance'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChallans()
    {
        return $this->hasMany(Challan::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCity()
    {
        return $this->hasOne(City::className(), ['city_id' => 'client_city']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientPaymentDetails()
    {
        return $this->hasMany(ClientPaymentDetails::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientPaymentHistories()
    {
        return $this->hasMany(ClientPaymentHistory::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientSalesPayments()
    {
        return $this->hasMany(ClientSalesPayment::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketBookSalesDetails()
    {
        return $this->hasMany(MarketBookSalesDetails::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasMany(Sales::className(), ['client_id' => 'client_id']);
    }

    public function getOutlet()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }
}
