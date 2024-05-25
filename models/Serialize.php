<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;

/**
 * This is the model class for table "serialize".
 *
 * @property integer $id
 * @property string $source
 * @property integer $refId
 * @property string $data
 * @property integer $created_by
 * @property integer $approved_by
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Serialize extends \yii\db\ActiveRecord
{
    const  STATUS_PENDING='pending';
    const  STATUS_APPVORD='approved';
    const  STATUS_DELETE='delete';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'serialize';
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
            [['source', 'refId', 'data', 'created_by'], 'required'],
            [['source', 'data'], 'string'],
            [['refId', 'created_by', 'approved_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'source' => Yii::t('app', 'Source'),
            'refId' => Yii::t('app', 'Ref ID'),
            'data' => Yii::t('app', 'Data'),
            'created_by' => Yii::t('app', 'Creator'),
            'approved_by' => Yii::t('app', 'Approved'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
        ];
    }

    public static function decrypt($data)
    {
        return Json::decode($data);
    }

    public static function encrypt($data)
    {
        return Json::encode($data);
    }

    public static function getTable($name)
    {
        return preg_replace("/[^a-zA-Z0-9_]+/", "", $name);
    }

    public static function add($refId , $table, Model $model, $status=self::STATUS_PENDING)
    {
        $serialize = new self();
        $serialize->id = null;
        $serialize->source = self::getTable($table);
        $serialize->refId = $refId;
        $serialize->data = self::encrypt($model->getAttributes());
        $serialize->created_by = Yii::$app->user->getId();
        $serialize->approved_by = null;
        $serialize->status = $status;
        if($serialize->save()){
            return true;
        }else{
            return false;
        }
    }

}
