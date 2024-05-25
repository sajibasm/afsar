<?php

namespace app\controllers;

use app\components\FlashMessage;
use app\components\Utility;
use Yii;
use app\models\SalesDraft;
use app\models\SalesDraftSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SalesDraftController implements the CRUD actions for SalesDraft model.
 */
class SalesDraftController extends Controller
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
     * Lists all SalesDraft models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SalesDraftSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalesDraft model.
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
     * Creates a new SalesDraft model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalesDraft();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->sales_details_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing SalesDraft model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->sales_details_id]);
        }
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('update', [
                'model' => $model,
            ]);

        }

        return $this->render('_salesDraft', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SalesDraft model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateDraft($id)
    {
        $model = $this->findModel($id);

        return $this->renderAjax('_salesDraft', [
            'model' => $model,
        ]);

    }

    /**
     * Finds the SalesDraft model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalesDraft the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalesDraft::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
