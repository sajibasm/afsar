<?php

namespace app\controllers;

use app\components\CashUtility;
use app\components\DateTimeUtility;
use DateTime;
use kartik\mpdf\Pdf;
use Yii;
use app\models\CashBook;
use app\models\CashBookSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CashBookController implements the CRUD actions for CashBook model.
 */
class CashBookController extends Controller
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
     * Lists all CashBook models.
     * @return mixed
     */
    public function actionSummery($pdf=false)
    {
        if($pdf){
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
                'content' => $this->renderPartial('_summery',['pdf'=>true]),
                'options' => [
                    'title' => 'Cash Summery - Afsar Traders',  //TODO need to add shop name from database config.
                    'subject' => 'Generating PDF files by Axial Solution Ltd.'
                ],
                'methods' => [
                    'SetHeader' => ['Afsar Traders||Generated On: ' . DateTimeUtility::getDate( null, 'd/m/Y h:i:sA' )],
                    'SetFooter' => ['Developed By: Axial Solution Ltd.| |Page {PAGENO}'],
                ]
            ]);


            // return the pdf output as per the destination setting
            return $pdf->render();
        }

        return $this->render('_summery', ['pdf'=>false]);
    }

    /**
     * Lists all CashBook models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CashBookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CashBook model.
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
     * Creates a new CashBook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CashBook();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CashBook model.
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
     * Finds the CashBook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CashBook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CashBook::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
