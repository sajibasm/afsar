<?php

namespace mdm\admin\controllers;

use app\components\GoogleCaptcha;
use app\components\UserUtility;
use app\models\UserIpWhitelist;
use app\models\UserLoginLogs;
use mdm\admin\components\UserStatus;
use mdm\admin\models\form\ChangePassword;
use mdm\admin\models\form\Login;
use mdm\admin\models\form\PasswordResetRequest;
use mdm\admin\models\form\ResetPassword;
use mdm\admin\models\form\Signup;
use mdm\admin\models\searchs\User as UserSearch;
use mdm\admin\models\User;
use Yii;
use yii\base\UserException;
use yii\filters\VerbFilter;
use yii\mail\BaseMailer;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * User controller
 */
class UserController extends Controller
{
    private $_oldMailPath;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'logout' => ['post'],
                    'activate' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (Yii::$app->has('mailer') && ($mailer = Yii::$app->getMailer()) instanceof BaseMailer) {
                /* @var $mailer BaseMailer */
                $this->_oldMailPath = $mailer->getViewPath();
                $mailer->setViewPath('@mdm/admin/mail');
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterAction($action, $result)
    {
        if ($this->_oldMailPath !== null) {
            Yii::$app->getMailer()->setViewPath($this->_oldMailPath);
        }
        return parent::afterAction($action, $result);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
                'model' => $this->findModel($id),
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionLoginLog()
    {
        $this->layout = '@app/themes/adminlte/layouts/main.php';

        $userId = Yii::$app->user->id;
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\UserLoginLogs::find()
                ->where(['user_id' => $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(10), // ✅ Show only last 10
            'pagination' => false, // ✅ No pager, exactly 10
        ]);

        return $this->render('login-log', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Login
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = '@app/themes/adminlte/layouts/main-login.php';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new Login();
        $model->username = 'superadmin'; // Default username for login
        $model->password = '123456'; // Default password for login

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            $captchaResponse = Yii::$app->request->post('g-recaptcha-response');
            if (!GoogleCaptcha::validation($captchaResponse, getenv('GOOGLE_CAPTCHA_SECRET_KEY'))) {
                Yii::$app->session->setFlash('error', 'Captcha validation failed.');
                return $this->render('@app/views/layouts/login', ['model' => $model]);
            }

            $user = \app\models\User::findByUsername($model->username);
            if ($user && $user->validatePassword($model->password)) {
                if ($user->is_2fa_enabled) {
                    // Temporarily store user ID to validate OTP next
                    Yii::$app->session->set('pending_2fa_user_id', $user->id);
                    return $this->redirect(['/admin/user/verify-2fa']);
                }

                // No 2FA, normal login
                if(Yii::$app->userLoginLogger->logLogin($user)){
                    if ($model->login()) {
                        return $this->goBack();
                    }
                }else{
                    Yii::$app->session->setFlash('error', 'We detected a login from a new location. Please check your email to confirm if this was you.');
                }
            }else{
                Yii::$app->session->setFlash('error', 'Invalid login credentials.');
            }
        }

        return $this->render('@app/views/layouts/login', ['model' => $model]);
    }

    public function actionIpConfirm($uid, $ip, $token)
    {
        $expected = hash_hmac('sha256', $uid . $ip, Yii::$app->params['secretKey']);
        if (!hash_equals($expected, $token)) {
            throw new ForbiddenHttpException('Invalid or expired token.');
        }

        $user = \app\models\User::findIdentity($uid);
        $userIpWhitelist = UserIpWhitelist::find()->where(['user_id' => $uid, 'ip_address' => $ip])->one();
        if($userIpWhitelist && $user){
            $location = Yii::$app->userLoginLogger->getLocationFromIP($ip);
            if ($location) {
               $isUserLoginSave =  Yii::$app->userLoginLogger->addUserLoginLog($user->user_id, $location, $userIpWhitelist->user_agent);
                if($isUserLoginSave){
                    // ✅ Delete all whitelist records for the user
                    UserIpWhitelist::deleteAll(['user_id' => $uid]);
                    Yii::$app->session->setFlash('success', 'Ip address confirmed successfully.');
                    return $this->redirect(['/admin/user/login']);
                }
            }
        }

        Yii::$app->session->setFlash('error', 'Invalid request or user not found.');
        return $this->redirect(['/admin/user/login']);
    }

    /**
     * Verify 2FA code
     * @return string
     */

    public function actionVerify2fa()
    {
        $this->layout = '@app/themes/adminlte/layouts/main-login.php';
        $userId = Yii::$app->session->get('pending_2fa_user_id');

        if (!$userId || !($user = \app\models\User::findOne($userId))) {
            return $this->redirect(['login']);
        }

        $model = new \yii\base\DynamicModel(['otp']);
        $model->addRule('otp', 'required')
            ->addRule('otp', 'string', ['length' => 6]);

        $tfa = new \RobThree\Auth\TwoFactorAuth('Afsar ERP');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($tfa->verifyCode($user->auth_2fa_secret, $model->otp)) {
                Yii::$app->user->login($user, 3600*24*30);
                Yii::$app->session->remove('pending_2fa_user_id');
                Yii::$app->userLoginLogger->logLogin($user);
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Invalid OTP code.');
            }
        }

        return $this->render('verify-otp', ['model' => $model]);
    }

    public function actionActivate2fa()
    {
        $this->layout = '@app/themes/adminlte/layouts/main.php';

        $user = Yii::$app->user->identity;
        $tfa = $user->getTwoFactorAuth();

        if (!Yii::$app->session->has('2fa_temp_secret')) {
            return $this->redirect(['profile']);
        }

        $user->temp_2fa_secret = Yii::$app->session->get('2fa_temp_secret');
        $qrCodeUrl = $tfa->getQRCodeImageAsDataUri($user->username, $user->temp_2fa_secret);

        if (Yii::$app->request->isPost && $user->load(Yii::$app->request->post())) {
            if (empty($user->otp_input)) {
                Yii::$app->session->setFlash('error', 'Please enter the verification code.');
            } elseif (!$tfa->verifyCode($user->temp_2fa_secret, $user->otp_input)) {
                Yii::$app->session->setFlash('error', 'Invalid code. Please try again.');
            } else {
                $user->auth_2fa_secret = $user->temp_2fa_secret;
                $user->is_2fa_enabled = true;
                Yii::$app->session->remove('2fa_temp_secret');
                if ($user->save(false)) {
                    Yii::$app->session->setFlash('success', '2FA enabled successfully.');
                    return $this->redirect(['profile']);
                }
            }
        }

        return $this->render('activate-2fa.php', [
            'model' => $user,
            'qrCodeUrl' => $qrCodeUrl
        ]);
    }

    public function actionProfile()
    {
        $this->layout = '@app/themes/adminlte/layouts/main.php';

        $model = Yii::$app->user->identity;
        $model->scenario = 'update-password';

        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->password)) {
                $model->setPassword($model->password);
                $model->generateAuthKey();
            }

            if ($model->is_2fa_enabled == 0){
                $model->auth_2fa_secret = null; // Disable 2FA
                $model->is_2fa_enabled = 0;
            }

            // If user checked to enable 2FA and no secret saved yet
            if ($model->is_2fa_enabled && empty($model->auth_2fa_secret)) {
                $tfa = new \RobThree\Auth\TwoFactorAuth('Afsar ERP');
                $secret = $tfa->createSecret();
                Yii::$app->session->set('2fa_temp_secret', $secret);
                return $this->redirect(['/admin/user/activate-2fa']);
            }

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Profile updated.');
                return $this->redirect(['profile']);
            }
        }

        return $this->render('profile',  ['model' => $model]);
    }

    /**
     * Logout
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout();
        return $this->goHome();
    }


    /**
     * Request reset password
     * @return string
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = '@app/themes/adminlte/layouts/main-login.php'; // or your custom layout

        $model = new PasswordResetRequest();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
                'model' => $model,
        ]);
    }

    /**
     * Reset password
     * @return string
     */
    public function actionResetPassword($token)
    {
        $this->layout = '@app/themes/adminlte/layouts/main-login.php'; // or your custom layout

        try {
            $model = new ResetPassword($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
                'model' => $model,
        ]);
    }

    /**
     * Reset password
     * @return string
     */
    public function actionChangePassword()
    {
        $model = new ChangePassword();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->change()) {
            return $this->goHome();
        }
        return $this->render('change-password', [
                'model' => $model,
        ]);
    }

    /**
     * Activate new user
     * @param integer $id
     * @return type
     * @throws UserException
     * @throws NotFoundHttpException
     */
    public function actionActivate($id)
    {
        /* @var $user User */
        $user = $this->findModel($id);
        if ($user->status == UserStatus::INACTIVE) {
            $user->status = UserStatus::ACTIVE;
            if ($user->save()) {
                return $this->goHome();
            } else {
                $errors = $user->firstErrors;
                throw new UserException(reset($errors));
            }
        }
        return $this->goHome();
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
