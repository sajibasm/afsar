<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%customer_withdraw}}".
 *
 * @property integer $id
 * @property integer $payment_history_id
 * @property integer $outletId
 * @property integer $client_id
 * @property double $amount
 * @property string $remarks
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $status
 * @property string $type
 * @property string $extra
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Client $customer
 * @property User $updatedUser
 * @property User $user
 */
class CustomerWithdraw extends \yii\db\ActiveRecord
{

    const  STATUS_PENDING = 'Pending';
    const  STATUS_APPROVED = 'Approved';
    const  STATUS_DECLINED = 'Declined';
    const  STATUS_DELETE = 'Delete';

    const  TYPE_BANK = 'Bank';
    const  TYPE_CASH = 'Cash';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customer_withdraw}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function() { return DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka'); }
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_history_id', 'amount', 'created_by'], 'required'],
            [['payment_history_id', 'created_by', 'updated_by', 'client_id'], 'integer'],
            [['amount'], 'number'],
            [['status', 'extra'], 'string'],
            [['created_at', 'updated_at', 'outletId'], 'safe'],
            [['remarks'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'payment_history_id' => Yii::t('app', 'Payment History ID'),
            'outletId' => Yii::t('app', 'Outlet'),
            'client_id' => Yii::t('app', 'Client'),
            'amount' => Yii::t('app', 'Amount'),
            'remarks' => Yii::t('app', 'Remarks'),
            'created_by' => Yii::t('app', 'CreatedBy'),
            'updated_by' => Yii::t('app', 'Approved'),
            'status' => Yii::t('app', 'Status'),
            'type' => Yii::t('app', 'Type'),
            'extra' => Yii::t('app', 'Extra'),
            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'created_by']);
    }

    public function getOutletDetail()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'updated_by']);
    }

}
