<?php

namespace app\controllers;

use app\components\FlashMessage;
use app\components\Utility;
use app\models\CashBook;
use app\modules\admin\components\Helper;
use DateTime;
use DateTimeZone;
use Yii;
use app\models\CashHandReceived;
use app\models\CashHandReceivedSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CashHandReceivedController implements the CRUD actions for CashHandReceived model.
 */
class CashHandReceivedController extends Controller
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

    /**
     * Lists all CashHandReceived models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CashHandReceivedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApproved($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        $model->status = CashHandReceived::STATUS_APPROVED;

        if($model->save()){

            $cash = CashBook::find()->where(['reference_id'=>$model->id, 'source'=>CashBook::SOURCE_CASH_HAND_RECEIVED])->one();
            if(!$cash){
                $cash = new CashBook();
            }

            $cash->outletId = $model->outletId;
            $cash->cash_in = $model->received_amount;
            $cash->cash_out = 0;
            $cash->source = CashBook::SOURCE_CASH_HAND_RECEIVED;
            $cash->ref_user_id = $model->user_id;
            $cash->reference_id = $model->id;
            $cash->remarks = $model->remarks;
            if($cash->save()){
                FlashMessage::setMessage("Cash Hand Received Amount#".$model->received_amount." has been approved", "Create Cash Hand Received", "info");
                return $this->redirect(['index']);
            }else{
                FlashMessage::setMessage("Something went wrong on cash hand received approval", "Create Cash Hand Received", "warning");
                return $this->redirect(['index']);
            }
        }
    }

    /**
     * Creates a new CashHandReceived model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CashHandReceived();
        $model->status = CashHandReceived::STATUS_PENDING;
        $model->user_id = Yii::$app->user->getId();

        if(Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if($model->save()){
                FlashMessage::setMessage("Cash Hand Received Amount#".$model->received_amount." has been created", "Create Cash Hand Received", "success");
                if(Yii::$app->asm->can('approved')){
                    return $this->redirect(['approved', 'id'=>Utility::encrypt($model->id)]);
                }else{
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    public function actionView($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        if($model->status==CashHandReceived::STATUS_PENDING){
            return $this->renderAjax('view', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CashHandReceived model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        if(Yii::$app->request->isPost) {
            if(Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->post());
                if($model->save()){
                    FlashMessage::setMessage("Cash Hand Received Amount#".$model->received_amount." has been updated", "Create Cash Hand Received", "primary");
                    if(Yii::$app->asm->can('approved')){
                        return $this->redirect(['approved', 'id'=>Utility::encrypt($model->id)]);
                    }else{
                        return $this->redirect(['index']);
                    }
                }
            }
        }

        return $this->render('update', [
                'model' => $model,
        ]);

    }

    /**
     * Finds the CashHandReceived model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CashHandReceived the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CashHandReceived::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
