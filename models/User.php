<?php

namespace app\models;

use app\components\DateTimeUtility;
use kartik\password\StrengthValidator;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $user_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string $email
 * @property string|null $password_hash
 * @property string|null $auth_key
 * @property string|null $password_reset_token
 * @property string $user_image
 * @property int $status
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 10;
    public $password;

    public  $enableAutoLogin = false;

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

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password_hash', 'status'], 'required', 'on'=>['create']],
            [['username', 'email'], 'unique', 'targetAttribute' => ['email', 'username']],
            [['email'], 'email'],

            [['password_hash'],
                StrengthValidator::className(),
                'min'=>8, 'digit'=>1, 'special'=>0,
                'preset'=>StrengthValidator::NORMAL
            ],

            [['status'], 'integer'],
            [['created_at', 'updated_at', 'user_image'], 'safe'],
            [['first_name', 'last_name', 'username'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
            [['password_hash', 'auth_key', 'password_reset_token', 'user_image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'password_hash' => Yii::t('app', 'Password'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'user_image' => Yii::t('app', 'Image'),
            'status' => Yii::t('app', 'Status'),
            'password' => Yii::t('app', 'Password'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }


    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @param null $type
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function getUserOutletDetail()
    {
        return $this->hasMany(UserOutlet::className(), ['userId' => 'user_id']);
    }

}