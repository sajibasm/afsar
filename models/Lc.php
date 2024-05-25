<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%lc}}".
 *
 * @property integer $lc_id
 * @property string $lc_name
 * @property string $lc_number
 * @property string $remarks
 * @property integer $branch_id
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Branch $branch
 * @property User $lcUser
 * @property ProductStock[] $productStocks
 * @property ProductStockDraft[] $productStockDrafts
 */
class Lc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lc}}';
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
            [[ 'branch_id', 'lc_name', 'lc_number'], 'required'],
            [['lc_id', 'branch_id', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['lc_name'], 'string', 'max' => 100],
            [['remarks'], 'string', 'max' => 200],
            [['lc_number'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lc_id' => Yii::t('app', 'ID'),
            'lc_name' => Yii::t('app', 'Name'),
            'lc_number' => Yii::t('app', 'Number'),
            'remarks' => Yii::t('app', 'Remarks'),
            'branch_id' => Yii::t('app', 'Branch '),
            'user_id' => Yii::t('app', 'User'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['branch_id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLcUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStocks()
    {
        return $this->hasMany(ProductStock::className(), ['lc_id' => 'lc_id']);
    }


    public function getBranchList($withBankConcat = false)
    {
        $list = [];
        $branchs = Branch::find()->orderBy('bank_id')->all();

        if($withBankConcat){
            foreach($branchs as $branch){

                $list[$branch->branch_id] = $branch->bank->bank_name.' - '.$branch->branch_name;
            }
        }else{
            foreach($branchs as $branch){
                $list[] = [$branch->branch_id = $branch->branch_name];
            }
        }
        return $list;
    }
}
