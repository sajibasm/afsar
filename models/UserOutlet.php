<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_outlet".
 *
 * @property int $userOutletId
 * @property int $userId
 * @property int $outletId
 * @property int $createdBy
 * @property int $updatedBy
 * @property string $createdAt
 * @property string $updatedAt
 */
class UserOutlet extends \yii\db\ActiveRecord
{

    public $outlet = [];

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
                'value' => function () {
                    return DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka');
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_outlet';
    }


    public function beforeSave($insert)
    {
        foreach ($this->attributes as $attribute => $value) {
            if (is_string($value)) {
                $this->$attribute = trim($value);
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'outletId', 'createdBy', 'updatedBy', 'outlet'], 'required','on'=>['assign']],
            [['userId', 'outletId', 'createdBy', 'updatedBy'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            ['outlet', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'userOutletId' => 'User Store ID',
            'userId' => 'User ID',
            'outlet' => 'Store',
            'outletId' => 'Store ID',
            'createdBy' => 'Created By',
            'updatedBy' => 'Updated By',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    public static function userOutletSession($userId)
    {
        $session = Yii::$app->session;
        $data = UserOutlet::find()->select(['userOutletId', 'userId', 'outletId'])->where(['userId' => $userId])->with(['outletDetail'])->all();
        $outlets = [];
        foreach ($data as $datum) {
            $outlets[$datum['outletDetail']['outletId']] = $datum['outletDetail']['name'];
        }

        $session['outlets'] = [$userId=>$outlets];
    }

    public function getOutletDetail()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId'])->where(['status' => 1]);
    }
}
