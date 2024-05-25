<?php

namespace app\controllers;
use app\components\CustomerUtility;
use app\components\FlashMessage;
use app\components\OutletUtility;
use app\components\Utility;
use Yii;
use app\models\Client;
use app\models\ClientSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
{
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
     * @param \yii\base\Action $event
     * @return bool|Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($event){
        if(Yii::$app->asm->has()){
            return parent::beforeAction($event);
        }else{
            return Yii::$app->user->isGuest? $this->redirect(['/site/login']): $this->redirect(['/site/permission']);
        }
    }

    public function actionByOutlet()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(Yii::$app->request->isPost){
            $data =  Yii::$app->request->post('depdrop_parents');
           if(count($data)>0){
               $out = [];
              $clients =  CustomerUtility::getCustomerWithAddressList(Client::CUSTOMER_TYPE_REGULAR, 'client_name', true, $data[0]);
              foreach ($clients as $key=>$val){
                  $out[] = ['id'=>$key, 'name'=>$val];
              }
              return ['output' => $out, 'selected' => ''];
           }
        }

        return ['output' => '', 'selected' => ''];
    }

    public function actionTotalDuesByCustomer()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->request->isAjax){
            $customerId = Yii::$app->request->post('customer');
            $outletId = Yii::$app->request->post('customer');
            return['error'=>false, 'amount'=>CustomerUtility::getTotalDuesByCustomer($customerId, $outletId)];
        }else{
            return['error'=>true, 'amount'=>0];
        }
    }

    /**
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Client model.
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
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client();
        if(OutletUtility::numberOfOutletByUser()===1){
            $model->outletId = OutletUtility::defaultOutletByUser();
        }


        if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            if ($model->save()) {
                FlashMessage::setMessage('Customer: <strong>' . $model->client_name . '</strong> has been added.', "Customer", "success");
                return $this->redirect(['index']);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Utility::decrypt($id));

        if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            if ($model->save()) {
                FlashMessage::setMessage('Customer: <strong>' . $model->client_name . '</strong> has been updated.', "Customer", "success");
                return $this->redirect(['index']);
            }

        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
