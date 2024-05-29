<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%cash_hand_received}}".
 *
 * @property integer $id
 * @property integer $outletId
 * @property integer $user_id
 * @property double $received_amount
 * @property string $remarks
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 *
 *  @property User $user
 */
class CashHandReceived extends \yii\db\ActiveRecord
{

    const  STATUS_PENDING = 'Pending';
    const  STATUS_APPROVED = 'Approved';
    const  STATUS_DECLINED = 'Declined';
    const  STATUS_DELETE = 'Delete';

    public $created_to;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cash_hand_received}}';
    }

    /**
     * @return array
     */
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
            [['received_amount', 'outletId'], 'required'],
            [['status'], 'string'],
            [['user_id'], 'integer'],
            [['received_amount'], 'number', 'min'=>1],
            [['created_at', 'updated_at', 'created_to'], 'safe'],
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
            'outletId' => Yii::t('app', 'Outlet'),
            'user_id' => Yii::t('app', 'User ID'),
            'received_amount' => Yii::t('app', 'Received Amount'),
            'remarks' => Yii::t('app', 'Remarks'),
            'created_at' => Yii::t('app', 'Created At'),
            'status' => Yii::t('app', 'Status'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_to' => Yii::t('app', 'To'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function getOutlet()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }


}
