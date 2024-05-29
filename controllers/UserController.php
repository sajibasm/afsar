<?php

namespace app\controllers;

use app\components\ConstrainUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\UserOutlet;
use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\base\Model;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "User #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    public function actionOutlet($id)
    {
        $userId = Utility::decrypt($id);
        $model = new UserOutlet();
        $model->setScenario('assign');
        if (Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            try {

//                *
//                * @property int $userOutletId
//                * @property int $userId
//                * @property int $outletId
//                * @property int $createdBy
//                * @property int $updatedBy
//                * @property string $createdAt
//                * @property string $updatedAt

                // Begin a transaction
                $transaction = Yii::$app->db->beginTransaction();
                $data = [];
                $columns = ['userId', 'outletId', 'createdBy', 'updatedBy'];
                UserOutlet::deleteAll(['userId'=>$userId]);

                foreach ($model->outlet as $outletId) {
                    $data[] =  [ $userId, $outletId, Yii::$app->user->identity->id, Yii::$app->user->identity->id];
                }
                // Execute the bulk insert
                Yii::$app->db->createCommand()->batchInsert(UserOutlet::tableName(), $columns, $data)->execute();
                // Commit the transaction
                $transaction->commit();
                $message = "Outlet has been assign";
                FlashMessage::setMessage($message, 'Item', "success");
                $this->redirect(['index']);

            } catch (\Exception $e) {
                // Rollback the transaction if any error occurs
                $transaction->rollBack();
                Yii::error("Bulk insert failed: " . $e->getMessage(), __METHOD__);
                $message = 'Bulk insert failed: ' . $e->getMessage();
                FlashMessage::setMessage($message, 'Item', "success");
                $this->redirect(['index']);
            }
        }

        return $this->render('outlet', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new User model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new User();
        $model->setScenario('create');
        $model->status = ConstrainUtility::USER_ACTIVE_STATUS;
        if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            $model->password_hash = Yii::$app->security->generatePasswordHash($model->password_hash);
            if($model->save()){
                $message = "User has created successfully";
                FlashMessage::setMessage($message, 'Item', "success");
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model   = $this->findModel(Utility::decrypt($id));
        $model->password = $model->password_hash;
        $model->password_hash = '';
        $model->status  = ConstrainUtility::USER_ACTIVE_STATUS;
        if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());

            if(!empty($model->password_hash)){
                $model->password_hash = Yii::$app->security->generatePasswordHash($model->password_hash);
            }else{
                $model->password_hash = $model->password;
            }
            if($model->save()){
                $message = "User has updated successfully";
                FlashMessage::setMessage($message, 'Item', "success");
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
