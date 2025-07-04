<?php

namespace app\controllers;

use app\components\FlashMessage;
use app\components\Utility;
use Yii;
use app\models\Lc;
use app\models\LcSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

/**
 * LcController implements the CRUD actions for Lc model.
 */
class LcController extends Controller
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
     * Lists all Lc models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LcSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lc model.
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
     * Creates a new Lc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Lc();
        $model->user_id = Yii::$app->user->getId();

        if(Yii::$app->request->isPost){
        $model->load(Yii::$app->request->post());
            if($model->save()){
                FlashMessage::setMessage(
                    'LC #' . trim($model->lc_name) . ' has been created.',
                    'LC Created',
                    'success'
                );
                return $this->redirect(['lc/index']);
            }
        }

        return $this->render('create', [
                'model' => $model,
        ]);

    }

    /**
     * Updates an existing Lc model.
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
                FlashMessage::setMessage(
                    'LC #' . trim($model->lc_name) . ' has been updated.',
                    'LC Created',
                    'success'
                );
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Lc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lc the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lc::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
