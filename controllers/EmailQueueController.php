<?php

namespace app\controllers;

use app\components\Mail;
use app\components\PdfGen;
use app\components\Utility;
use app\models\Template;
use Yii;
use app\models\EmailQueue;
use app\models\EmailQueueSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmailQueueController implements the CRUD actions for EmailQueue model.
 */
class EmailQueueController extends Controller
{
    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['queue'],
                        'allow' => true,
                    ],

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



    public function actionQueue()
    {
        $models = EmailQueue::find()->where(['status'=>EmailQueue::STATUS_PENDING])->orderBy('id')->limit(5)->all();

        foreach ($models as $model){
            if($model->template==EmailQueue::TEMPLATE_INVOICE){
                if(Mail::sendInvoice($model->extra_params)){
                    $model->status = EmailQueue::STATUS_SENT;
                    $model->save();
                }
            }elseif($model->template==EmailQueue::TEMPLATE_PAYMENT_RECEIPT){
                if(Mail::sendPaymentReceipt($model->extra_params)){
                    $model->status = EmailQueue::STATUS_SENT;
                    $model->save();
                }
            }
        }
    }

    /**
     * Lists all EmailQueue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmailQueueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EmailQueue model.
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
     * Creates a new EmailQueue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EmailQueue();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EmailQueue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Finds the EmailQueue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmailQueue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmailQueue::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
