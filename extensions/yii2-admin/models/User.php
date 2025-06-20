<?php

namespace mdm\admin\models;

use mdm\admin\components\Configs;
use mdm\admin\components\UserStatus;
use RobThree\Auth\TwoFactorAuth;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $user_id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property boolean $is_2fa_enabled
 * @property string $auth_2fa_secret
 *
 * @property UserProfile $profile
 */


class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 10;

    public $password;
    public $confirm_password;

    public $otp_input; // entered by user
    public $temp_2fa_secret; // for view only — not saved to DB


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Configs::instance()->userTable;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'in', 'range' => [UserStatus::ACTIVE, UserStatus::INACTIVE]],
            [['username', 'email'], 'safe'],
            ['is_2fa_enabled', 'boolean'],
            ['auth_2fa_secret', 'string'],
            ['first_name', 'string'],
            ['last_name', 'string'],

            // Only required if enabling 2FA
            ['otp_input', 'required', 'when' => function ($model) {
                return $model->is_2fa_enabled && empty($model->auth_2fa_secret);
            }, 'whenClient' => "function (attribute, value) {
            return $('#user-is_2fa_enabled').is(':checked') && $('#user-auth_2fa_secret').val() === '';
        }"],

            ['otp_input', 'string'],

            [['email'], 'email'],
            [['email'], 'required', 'when' => function ($model) {
                return empty($model->email); // require only if email is currently empty
            }, 'whenClient' => "function (attribute, value) {
            return $('#user-email').val() === '';
        }"],

            // Password update validation
            [['password', 'confirm_password'], 'string', 'min' => 6, 'on' => 'update-password'],
            ['confirm_password', 'compare', 'compareAttribute' => 'password', 'message' => "Passwords don't match", 'on' => 'update-password'],
        ];
    }


    public function attributeLabels()
    {
        return [
            // ... other labels
            'is_2fa_enabled' => 'Enable Two-Factor Authentication',
        ];
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id, 'status' => UserStatus::ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => UserStatus::ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
                'password_reset_token' => $token,
                'status' => UserStatus::ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }


    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function getDb()
    {
        return Configs::userDb();
    }

    // For 2FA

    public function getTwoFactorAuth()
    {
        return new TwoFactorAuth('Afsar ERP System');
    }

    public function generate2FASecret()
    {
        $tfa = $this->getTwoFactorAuth();
        $this->auth_2fa_secret = $tfa->createSecret();
        return $this->auth_2fa_secret;
    }

    public function getQRCodeImageUrl()
    {
        if (!$this->auth_2fa_secret) {
            $this->generate2FASecret();
        }
        $tfa = $this->getTwoFactorAuth();
        return $tfa->getQRCodeImageAsDataUri($this->username, $this->auth_2fa_secret);
    }

    public function validate2FACode($code)
    {
        $tfa = $this->getTwoFactorAuth();
        return $tfa->verifyCode($this->auth_2fa_secret, $code);
    }

    public function get2FASecret()
    {
        return $this->auth_2fa_secret;
    }

    public function set2FASecret($secret)
    {
        $this->auth_2fa_secret = $secret;
    }

}
