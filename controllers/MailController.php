<?php

namespace app\controllers;

use app\components\Mail;
use app\components\PdfGen;
use app\components\Utils;
use app\models\Customers;
use Yii;
use app\models\Voucher;
use app\models\VoucherSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

/**
 * VoucherController implements the CRUD actions for Voucher model.
 */
class MailController extends Controller
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
     * Lists all Voucher models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VoucherSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Voucher model.
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
     * Finds the Voucher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Voucher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Voucher::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionPreview($id)
    {
        $model = $this->findModel($id);

        if (!$this->voucherMail(['voucher' => $model], null, 'preview')) {
            Yii::$app->session->setFlash('error', 'Voucher mail sending failed.');
        } else {
            Yii::$app->session->setFlash('message', 'Voucher mail sent successfully.');
        }

        $this->redirect(['index']);
    }

    public function actionSend($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {

            $data = Yii::$app->request->post();

            if (!$this->voucherMail(['voucher' => $model], $data, 'send')) {
                Yii::$app->session->setFlash('error', 'Voucher mail sending failed.');
            } else {
                Yii::$app->session->setFlash('message', 'Voucher mail sent successfully to the customer.');
            }

            $this->redirect(['index']);
        } else {
            return $this->render('_customMail', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $args
     * @param $additional
     * @param null $status
     * @return bool
     */
    protected function voucherMail($args, $additional, $status = null)
    {
        $mail = Mail::getInstance();
        if ($args['voucher']->type == 'Hotel') {
            $fileName = 'hotel-voucher';
            $pdfVoucher = PdfGen::makeHotelVoucher($args, $fileName);
            $mail->setTemplate('hotel-booking-confirmation');
        }

        $mail->setData($args);

        if ($status == 'preview') {
            $mail->setTo(Yii::$app->user->identity->email);
        } else {
            $mail->setTo(Customers::findOne(['id' => $args['voucher']->customerId])->email);
            if (!is_null($additional)) {
                if (!empty($additional['cc'])) {
                    $mail->addCc(explode(',', $additional['cc']));
                }
                if (!empty($additional['bcc'])) {
                    $mail->addBcc(explode(',', $additional['bcc']));
                }
            }
            $mail->addCc(Yii::$app->user->identity->email);
        }

        if (isset($args['voucher']->checkIn) || !empty($args['voucher']->checkIn)){
            $mail->setSubject('Confirmation for Booking ID # ' . $args['voucher']->bookingId . ' Check-in: ' . date('l jS \of F Y h:i:s A', strtotime($args['voucher']->checkIn)));
        }else{
            $mail->setSubject('Confirmation for Booking ID # ' . $args['voucher']->bookingId);
        }

        if ($args['voucher']->type == 'Hotel') {

        } else {

        }

        $mail->attach($pdfVoucher);

        if (!$mail->send()) {
            return false;
        }

        Utils::unlink($pdfVoucher);

        return true;
    }

    public function actionAddRoom($row)
    {
        $model = new Voucher();
        return $this->renderPartial('room', [
            'model' => $model,
            'row' => $row
        ]);
    }

    public function actionAddOperator($row)
    {
        $model = new Voucher();
        return $this->renderPartial('operator', [
            'model' => $model,
            'row' => $row
        ]);
    }
}
