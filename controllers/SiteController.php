<?php

namespace app\controllers;

use app\components\CashUtility;
use app\components\DepositUtility;
use app\components\GoogleCaptcha;
use app\components\SMS;
use app\components\UserUtility;
use app\components\Utility;
use app\models\UserOutlet;
use http\Client;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;

use yii\web\Response;

class SiteController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'permission', 'index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ]
            ]
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionPermission()
    {
        return $this->render('denied');
    }


    public function actionSalesPie($outlet)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return CashUtility::salesPie(Utility::decrypt($outlet));
    }

    public function actionExpensePie($outlet)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return CashUtility::expensePie(Utility::decrypt($outlet));
    }

    public function actionStore()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return CashUtility::storeWiseSales();
    }

    public function actionAnalytics($outlet, $type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($type === 'cash') {
            return CashUtility::analytics(Utility::decrypt($outlet), 'NOW');
        }
        return DepositUtility::analytics(Utility::decrypt($outlet), 'NOW');
    }

    public function actionSalesGrowth($outlet)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return CashUtility::dailySalesGrowth(Utility::decrypt($outlet));
    }

    public function actionChart($outlet)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return CashUtility::monthlySalesSummery(Utility::decrypt($outlet));
    }

    public function actionDailySummery($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return CashUtility::todaySummery(Utility::decrypt($id));
    }

    public function actionIndex()
    {
        $outlets = UserOutlet::find()->where(['userId' => Yii::$app->user->id])->with('outletDetail')->all();
        return $this->render('index', ['outlets' => $outlets]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if (Yii::$app->request->isPost) {
            if (GoogleCaptcha::validation(Yii::$app->request->post('g-recaptcha-response'), getenv('GOOGLE_CAPTCHA_SECRET_KEY'))) {
                $model->load(Yii::$app->request->post());
                if ($model->login()) {
                    UserUtility::removeCartItemsByUser();
                    return $this->goBack();
                }
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        $session = Yii::$app->session;
        UserUtility::removeCartItemsByUser();
        unset($session['outlets']);
        Yii::$app->user->logout();
        return $this->redirect('login');
    }

    public function actionSendMessage()
    {
        $sms = new SMS();
        $sms->send('hello', '8801723458494');
    }

}
