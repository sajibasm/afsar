<?php

namespace app\controllers;

use app\components\FlashMessage;
use app\components\Utility;
use app\models\Brand;
use app\models\BrandNew;
use Yii;
use app\models\Item;
use app\models\ItemSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

/**
 * ItemController implements the CRUD actions for Item model.
 */
class ItemController extends Controller
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
     * @return bool|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($event){
        if(Yii::$app->asm->has()){
            return parent::beforeAction($event);
        }else{
            return Yii::$app->user->isGuest? $this->redirect(['/site/login']): $this->redirect(['/site/permission']);
        }
    }

    /**
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new ItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Item model.
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
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $hasError = false;
        $model = new Item();
        $model->setScenario('create');

        if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            $brandNew = BrandNew::findOne($model->brand_id);

            $transaction = Yii::$app->db->beginTransaction();

            try{
                if($model->save()){
                    $brand = new Brand();
                    $brand->item_id = $model->item_id;
                    $brand->brand_id = $brandNew->id;
                    $brand->brand_name = $brandNew->name;
                    $brand->brand_status = Brand::STATUS_ACTIVE;
                    if($brand->save()){
                        $message = "Item: ".$model->item_name.", Brand: ".$brandNew->name." has been added.";
                        FlashMessage::setMessage($message, 'Item', "success");
                    }else{
                        $hasError = true;
                    }
                }else{
                    $hasError = true;
                }

                if(!$hasError){
                    $transaction->commit();
                }else{
                    $transaction->rollBack();
                }

                return $this->redirect(['item/index']);
            }catch (\Exception $e) {
                $transaction->rollBack();
                return $this->redirect(['item/index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Utility::decrypt($id));

        if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            if($model->save()){
                $message = "Item: ".$model->item_name." has been updated.";
                FlashMessage::setMessage($message, 'Item', "info");
                return $this->redirect(['item/index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Item::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
