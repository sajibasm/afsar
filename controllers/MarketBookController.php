<?php

namespace app\controllers;

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\ProductUtility;
use app\components\Utility;
use app\models\Client;
use app\models\CustomerAccount;
use app\models\MarketBookHistory;
use app\models\PaymentType;
use app\models\ProductStatement;
use app\models\Sales;
use app\models\SalesDetails;
use Yii;
use app\models\MarketBook;
use app\models\MarketBookSearch;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * MarketBookController implements the CRUD actions for MarketBook model.
 */
class MarketBookController extends Controller
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
     * Lists all MarketBook models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MarketBookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MarketBook model.
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
     * Creates a new MarketBook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MarketBook();
        $model->setScenario('customer');

        if ($model->load(Yii::$app->request->post())) {

            if($model->validate()){
                return $this->redirect(['sell', 'id' =>Utility::encrypt($model->client_id)]);
            }
        } else {
            return $this->render('customer', [
                'model' => $model,
            ]);
        }
    }

    public function actionCheckAvailableProduct()
    {
        $response = '';

        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request->post();
            if (isset($request['size_id']) && $request['size_id']!=0) {
                $sizeId = $request['size_id'];
                $qty = ProductUtility::getTotalQuantity($sizeId) - ProductUtility::getDraftProductQuantity($sizeId);
                $stockPrice = ProductUtility::getProductStockPrice($sizeId);

                if(isset($stockPrice->cost_price) && !empty($stockPrice->cost_price)){
                    $costPrice = $stockPrice->cost_price;
                }else
                    $costPrice = 0;
            }

            if (doubleval($qty) > 0) {
                $response = [
                    'isAvailable' => true,
                    'costAmount' => $costPrice,
                    'msg' => 'Available quantity is: <strong>' . doubleval($qty) . '</strong>'
                ];
            } else {
                $response = [
                    'error' => false,
                    'costAmount' => $costPrice,
                    'msg' => 'Available quantity is: <strong>' . doubleval($qty) . '</strong>'
                ];
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionReturnItem($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        $model->status = MarketBook::STATUS_RETURN;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $productStatement = new ProductStatement();
            $productStatement->item_id = $model->item_id;
            $productStatement->brand_id = $model->brand_id;
            $productStatement->size_id = $model->size_id;
            $productStatement->quantity = $model->quantity;
            $productStatement->type = ProductStatement::TYPE_MARKET_RETURN;
            $productStatement->remarks = $model->remarks;
            $productStatement->reference_id = $model->market_sales_id;
            $productStatement->user_id = $model->user_id;

            if($productStatement->save()){

                if($model->save()){
                    $transaction->commit();
                }else{
                    $transaction->rollBack();
                }

            }else{
                $transaction->rollBack();
            }


        } catch (\Exception $e) {
            $transaction->rollBack();

        }
    }

    public function actionDraftUpdate($id)
    {
        $response = [];
        $model = MarketBook::findOne(Utility::decrypt($id));
        $model->price = $model->sales_amount;

        $availableQty = CustomerUtility::marketReturnableQty($model->client_id, $model->size_id);
        $model->returnQuantity = $availableQty;

        if(Yii::$app->request->isPost){

            $quantity = $model->quantity;
            $model->load(Yii::$app->request->post());
            $model->quantity = $model->returnQuantity;
            $model->sales_amount = $model->price;
            $model->total_amount =  $model->sales_amount * $model->quantity;

            if($model->validate()){

                $connection = Yii::$app->db;
                $transaction = $connection->beginTransaction();

                try {

                    if($model->returnQuantity>0){

                        $marketBook = new MarketBook();
                        $marketBook->client_id = $model->client_id;
                        $marketBook->item_id = $model->item_id;
                        $marketBook->brand_id = $model->brand_id;
                        $marketBook->size_id = $model->size_id;
                        $marketBook->unit = $model->unit;
                        $marketBook->cost_amount = $model->cost_amount;
                        $marketBook->sales_amount = $model->sales_amount;
                        $marketBook->quantity = $model->quantity;
                        $marketBook->total_amount = ($model->sales_amount * $marketBook->quantity);
                        $marketBook->user_id = Yii::$app->user->getId();
                        $marketBook->remarks = $model->remarks;
                        $marketBook->status = MarketBook::STATUS_RETURN;

                        if($marketBook->save()){

                            $productStatement = new ProductStatement();
                            $productStatement->item_id = $model->item_id;
                            $productStatement->brand_id = $model->brand_id;
                            $productStatement->size_id = $model->size_id;
                            $productStatement->type = ProductStatement::TYPE_MARKET_RETURN;
                            $productStatement->remarks = $model->remarks;
                            $productStatement->reference_id = $model->market_sales_id;
                            $productStatement->user_id = $model->user_id;
                            $productStatement->quantity = $model->quantity;

                            if($productStatement->save()){
                                $transaction->commit();
                                $response = ['error'=>false, 'msg'=>'Success'];
                            }else{
                                $transaction->rollBack();
                                $response = ['error'=>true, 'msg'=>ActiveForm::validate($productStatement)];
                            }

                        }else{
                            $response = ['error'=>true, 'msg'=>ActiveForm::validate($model)];
                        }

                    }

                } catch (\Exception $e) {
                    $transaction->rollBack();
                }

            }else{
                $response = ['error'=>true, 'msg'=>ActiveForm::validate($model)];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('_draftupdate', ['model'=>$model]);
        }
    }

    public function actionGenerateInvoice()
    {

        $invoiceId = [];
        $customer = [];
        $models = MarketBook::find()->select('DISTINCT(client_id) as clients')->all();

        foreach ($models as $model){

            $data = [];
            $deleteId = [];

            $items = MarketBook::find()->where(['client_id'=> $model->clients])->orderBy('market_sales_id ASC')->all();

            foreach ($items as $item){

                $totalAmount = 0;

                $deleteId[] = $item->market_sales_id;

                if($item->status==MarketBook::STATUS_SELL){

                    if(isset($data[$item->size_id])){
                        $quantity = ($data[$item->size_id]['quantity'] + $item->quantity);
                        $totalAmount = ($data[$item->size_id]['total_amount'] + ($item->sales_amount*$item->quantity));
                        $salesAmount = ($totalAmount/$quantity);
                        $data[$item->size_id] = [
                            'item_id'=>$item->item_id,
                            'brand_id'=>$item->brand_id,
                            'size_id'=>$item->size_id,
                            'unit'=>$item->unit,
                            'client_id'=>$item->client_id,
                            'cost_amount'=>$item->cost_amount,
                            'sales_amount'=>$salesAmount,
                            'total_amount'=>$totalAmount,
                            'quantity'=>$quantity,
                            'remarks'=>$item->remarks,
                        ];
                    }else{
                        $data[$item->size_id] = [
                            'item_id'=>$item->item_id,
                            'brand_id'=>$item->brand_id,
                            'size_id'=>$item->size_id,
                            'unit'=>$item->unit,
                            'client_id'=>$item->client_id,
                            'cost_amount'=>$item->cost_amount,
                            'sales_amount'=>$item->sales_amount,
                            'total_amount'=>($item->sales_amount*$item->quantity),
                            'quantity'=>$item->quantity,
                            'remarks'=>$item->remarks,
                        ];
                    }

                }else if($item->status==MarketBook::STATUS_RETURN){

                    if(isset($data[$item->size_id])){

                        $quantity = ($data[$item->size_id]['quantity'] - $item->quantity);
                        $totalAmount = ($data[$item->size_id]['total_amount'] - ($item->sales_amount*$item->quantity));
                        $salesAmount = ($totalAmount/$quantity);

                        $data[$item->size_id] = [
                            'item_id'=>$item->item_id,
                            'brand_id'=>$item->brand_id,
                            'size_id'=>$item->size_id,
                            'unit'=>$item->unit,
                            'client_id'=>$item->client_id,
                            'cost_amount'=>$item->cost_amount,
                            'sales_amount'=>$salesAmount,
                            'total_amount'=>$totalAmount,
                            'quantity'=>$quantity,
                            'remarks'=>$item->remarks,
                        ];
                    }else{
                        $data[$item->size_id] = [
                            'item_id'=>$item->item_id,
                            'brand_id'=>$item->brand_id,
                            'size_id'=>$item->size_id,
                            'unit'=>$item->unit,
                            'client_id'=>$item->client_id,
                            'cost_amount'=>$item->cost_amount,
                            'sales_amount'=>$item->sales_amount,
                            'total_amount'=>($item->sales_amount*$item->quantity),
                            'quantity'=>$item->quantity,
                            'remarks'=>$item->remarks,
                        ];
                    }
                }


                if(isset($customer[$item->client_id])){
                    $customer[$item->client_id] = $customer[$item->client_id] + $totalAmount;
                }else{
                    $customer[$item->client_id] = $totalAmount;
                }

            }

            $client = Client::findOne($model->clients);

            $sales = new Sales();
            $sales->memo_id  = null;
            $sales->client_id = $client->client_id;
            $sales->client_name = $client->client_name;
            $sales->client_mobile = $client->client_contact_number;
            $sales->contact_number = $client->client_contact_number;
            $sales->client_type = $client->client_contact_number;
            $sales->user_id = Yii::$app->user->getId();
            $sales->paid_amount = 0;
            $sales->due_amount = $customer[$client->client_id];
            $sales->discount_amount = 0;
            $sales->total_amount = $customer[$client->client_id];
            $sales->remarks = "Generated from Market Book";
            $sales->type = Sales::TYPE_SALES;
            $sales->payment_type = PaymentType::TYPE_CASH_ID;
            $sales->bank = 0;
            $sales->branch = 0;

            $db = \Yii::$app->db;
            $transaction = $db->beginTransaction();

            try {
                if($sales->save()){
                    $rows = [];

                    foreach ($data as $item){
                        $rows[] = [
                            'sales_details_id'=>null,
                            'sales_id'=>$sales->sales_id,
                            'item_id'=>$item['item_id'],
                            'brand_id'=>$item['brand_id'],
                            'size_id'=>$item['size_id'],
                            'unit'=>$item['unit'],
                            'cost_amount'=>$item['cost_amount'],
                            'sales_amount'=>$item['sales_amount'],
                            'total_amount'=>$item['total_amount'],
                            'quantity'=>$item['quantity'],
                            'challan_unit'=>$item['unit'],
                            'challan_quantity'=>$item['quantity'],
                        ];
                    }

                    $salesDetails = new SalesDetails();
                    $record = Yii::$app->db->createCommand()->batchInsert(SalesDetails::tableName(), $salesDetails->attributes(), $rows)->execute();

                    $customerAccount = new CustomerAccount();
                    $customerAccount->sales_id = $sales->sales_id;
                    $customerAccount->memo_id = $sales->memo_id;
                    $customerAccount->client_id = $sales->client_id;
                    $customerAccount->type = CustomerAccount::TYPE_SALES;
                    $customerAccount->account = CustomerAccount::ACCOUNT_RECEIVABLE;
                    $customerAccount->debit = ($sales->total_amount - $sales->discount_amount);
                    $customerAccount->credit = 0;
                    $customerAccount->balance = $customerAccount->debit;
                    $customerAccount->payment_type = CustomerAccount::PAYMENT_TYPE_NA;
                    if($customerAccount->save()){

                        $historyRows = [];
                        foreach ($items as $item){
                            $historyRows[] = [
                                'market_sales_id'=>null,
                                'sales_id'=>$sales->sales_id,
                                'client_id'=>$client->client_id,
                                'item_id'=>$item->item_id,
                                'brand_id'=>$item->brand_id,
                                'size_id'=>$item->size_id,
                                'unit'=>$item->unit,
                                'cost_amount'=>$item->cost_amount,
                                'sales_amount'=>$item->sales_amount,
                                'total_amount'=>$item->total_amount,
                                'quantity'=>$item->quantity,
                                'user_id'=>$item->user_id,
                                'remarks'=>$item->remarks,
                                'created_at'=>$item->created_at,
                                'updated_at'=>$item->updated_at,
                                'status'=>$item->status
                            ];
                        }

                        $marketBookHistory = new MarketBookHistory();
                        $historyRecords = Yii::$app->db->createCommand()->batchInsert(MarketBookHistory::tableName(), $marketBookHistory->attributes(), $historyRows)->execute();

                        if($record==count($rows) && $historyRecords==count($historyRows)){

                            $sql = "DELETE FROM `market_book` WHERE market_sales_id IN (".implode(",", $deleteId).");";
                            $deletedRecords = $db->createCommand($sql)->execute();

                            if($deletedRecords==count($deleteId)){
                                $invoiceId[] = $sales->sales_id;
                                $transaction->commit();
                            }else{
                                $transaction->rollback();
                            }

                        }else{
                            $transaction->rollback();
                        }
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
            }
        }


        if(!empty($invoiceId)){
            Yii::$app->session->setFlash('success', 'New sales Invoice has been created from market book# <strong>'.implode(',', $invoiceId).'</strong>');
            return $this->redirect(['index']);
        }

    }

    public function actionAddProduct()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new MarketBook();
        $model->setScenario('sell');
        $model->user_id = Yii::$app->user->getId();
        $model->status = MarketBook::STATUS_SELL;
        $model->sales_id = null;

        if(Yii::$app->request->isPost){

            $model->load(Yii::$app->request->post());
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                if($model->save()){

                    $productStatement = new ProductStatement();
                    $productStatement->item_id = $model->item_id;
                    $productStatement->brand_id = $model->brand_id;
                    $productStatement->size_id = $model->size_id;
                    $productStatement->quantity = -$model->quantity;
                    $productStatement->type = ProductStatement::TYPE_MARKET_SELL;
                    $productStatement->remarks = $model->remarks;
                    $productStatement->reference_id = $model->market_sales_id;
                    $productStatement->user_id = $model->user_id;

                    if($productStatement->save()){
                        $transaction->commit();
                        return ["Error"=>false, "Message"=>'Success'];
                    }else{
                        $transaction->rollBack();
                        return ["Error"=>false, "Message"=>$productStatement->getErrors()];
                    }
                }else{
                    $transaction->rollBack();
                    return ["Error"=>false, "Message"=>$model->getErrors()];
                }

            } catch (\Exception $e) {
                $transaction->rollBack();
                return ["Error"=>false, "Message"=>$e];
            }

        }
    }

    /**
     * Creates a new MarketBook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionSell($id)
    {

        $clientId = Utility::decrypt($id);
        $client = Client::findOne($clientId);
        $client->city = $client->clientCity->city_name;

        $model = new MarketBook();
        $model->setScenario('sell');
        $model->user_id = Yii::$app->user->getId();
        $model->client_id = $clientId;
        $model->status = MarketBook::STATUS_SELL;

        $searchModel = new MarketBookSearch();
        $searchModel->client_id = $clientId;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('create', [
            'model' => $model,
            'client' => $client,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing MarketBook model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->market_sales_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the MarketBook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MarketBook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MarketBook::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
