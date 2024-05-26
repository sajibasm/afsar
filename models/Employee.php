<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%employee}}".
 *
 * @property integer $id
 * @property integer $designation
 * @property string $full_name
 * @property string $dob
 * @property string $picture
 * @property string $contact_number
 * @property string $email
 * @property string $present_address
 * @property string $permanent_address
 * @property double $salary
 * @property string $joining_date
 * @property string $remarks
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property EmployeeDesignation $designationModel
 */
class Employee extends \yii\db\ActiveRecord
{

    const  ACTIVE_STATUS = 1;
    const  INACTIVE_STATUS = 0;

    const  ACTIVE_STATUS_LABEL = 'Active';
    const  INACTIVE_STATUS_LABEL = 'Inactive';


    public $imageFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%employee}}';
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
            [['designation', 'full_name', 'dob', 'contact_number', 'present_address', 'permanent_address', 'salary', 'joining_date', 'status'], 'required'],
            [['designation', 'status'], 'integer'],
            [['dob', 'joining_date', 'created_at', 'updated_at'], 'safe'],
            [['email'], 'email'],
            [['salary'], 'number'],
            [['full_name', 'picture', 'email'], 'string', 'max' => 100],
            [['contact_number'], 'string', 'max' => 20],
            [['present_address', 'permanent_address'], 'string', 'max' => 300],

            [['imageFile'], 'file', 'skipOnEmpty'=>true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'designation' => Yii::t('app', 'Designation'),
            'full_name' => Yii::t('app', 'Full Name'),
            'dob' => Yii::t('app', 'Date Of Birth'),
            'picture' => Yii::t('app', 'Picture'),
            'contact_number' => Yii::t('app', 'Contact Number'),
            'email' => Yii::t('app', 'Email'),
            'present_address' => Yii::t('app', 'Present Address'),
            'permanent_address' => Yii::t('app', 'Permanent Address'),
            'salary' => Yii::t('app', 'Salary'),
            'joining_date' => Yii::t('app', 'Joining Date'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),

            'imageFile' => Yii::t('app', 'Image'),
        ];
    }



    public function upload()
    {
        if ($this->validate()) {
            if(isset($this->imageFile->extension)){
                $this->picture = Yii::$app->security->generateRandomString(). '.' . $this->imageFile->extension;
                $this->imageFile->saveAs('uploads/employee/'.$this->picture);
                $this->imageFile = null;
            }
            return true;
        } else {
            return false;
        }
    }


    public function pictureRemove($picture)
    {
        if(!empty($picture) || $picture!=''){
            $imageLocation = Yii::$app->basePath."/web/uploads/employee/{$picture}";
            unlink($imageLocation);
        }
    }

    public function getImageUrl()
    {
        if($this->picture!=''){
            return Url::base(true).'/uploads/employee/'.$this->picture;
        }

        return Url::base(true).'/uploads/employee/user.png';
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDesignationModel()
    {
        return $this->hasOne(EmployeeDesignation::className(), ['id' => 'designation']);
    }


}
