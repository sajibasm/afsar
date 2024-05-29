<?php

namespace app\controllers;

use app\components\FlashMessage;
use app\components\ProductUtility;
use app\components\Utility;
use Yii;
use app\models\Size;
use app\models\SizeSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * SizeController implements the CRUD actions for Size model.
 */
class SizeController extends Controller
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

    public function actionGetBrandListByItem()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $itemId = $parents[0];
                $brands = ProductUtility::getBrandListByItem($itemId);
                foreach($brands as $brand){
                    $out[] = ['id'=>$brand->brand_id, 'name'=>$brand->brand_name];
                }
                return Json::encode(['output'=>$out, 'selected'=>'']);
            }
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
    }

    /**
     * Lists all Size models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SizeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

         return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        return $this->renderPartial('view', [
            'model' => $model,
        ]);

    }

    /**
     * Creates a new Size model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Size();

        if (Yii::$app->request->isPost) {

            $model->load(Yii::$app->request->post());
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if ($model->upload()) {
                if($model->save()){
                    FlashMessage::setMessage("Product Size: '.$model->size_name.' has been added.", "Approved Invoice", "info");
                    return $this->redirect(['index']);
                }
            }
        }
 
        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Size model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Utility::decrypt($id));

        if (Yii::$app->request->isPost) {

            $model->load(Yii::$app->request->post());
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if($model->imageFile){
               $currentFile = $model->size_image;
                if ($model->upload()) {
                    $model->saveThumb();
                    if($currentFile!=''){
                        $model->removeFile($currentFile);
                    }
                }
            }

            if($model->save()){
                FlashMessage::setMessage("Product Size: '.$model->size_name.' has been updated.", "Approved Invoice", "info");
                return $this->redirect(['index']);
            }

        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Finds the Size model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Size the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Size::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested size does not exist.');
    }
}
