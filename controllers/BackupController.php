<?php

namespace app\controllers;

use app\components\DBUtility;
use Yii;
use app\models\Backup;
use app\models\BackupSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BackupController implements the CRUD actions for Backup model.
 */
class BackupController extends Controller
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
     * Lists all Backup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BackupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Backup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        DBUtility::export("Manually create from admin panel by ".Yii::$app->user->identity->username);
        return $this->redirect(['index']);
    }


    public function actionDelete($id)
    {
        $model = Backup::findOne($id);
        $name = $model->name;
        $path = Yii::getAlias("@app/web/db/$name");

        chown($path, 666);

        if (unlink($path)) {
            echo "success";
        } else {
            echo "fail";
        }

        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Backup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Backup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Backup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
