<?php

namespace app\controllers;

use app\components\SystemSettings;
use app\components\CommonUtility;
use app\components\DateTimeUtility;
use app\components\EmployeeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\CashBook;
use app\models\DepositBook;
use app\models\Expense;
use app\models\ExpenseType;
use app\models\PaymentType;
use app\modules\admin\components\Helper;
use kartik\mpdf\Pdf;
use Yii;
use app\models\SalaryHistory;
use app\models\SalaryHistorySearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * SalaryHistoryController implements the CRUD actions for SalaryHistory model.
 */
class SalaryHistoryController extends Controller
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

    public function actionView($id)
    {
//        if(!Helper::checkRoute('approved')) {
//            return "You are not allowed to perform this action.";
//        }

        $model = $this->findModel(Utility::decrypt($id));

        if ($model->status == SalaryHistory::STATUS_PENDING) {

            if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                $json = (object)Json::decode($model->extra);
                $model->bank_id = $json->bank_id;
                $model->branch_id = $json->branch_id;
            }

            return $this->renderAjax('view', [
                'model' => $model,
            ]);

        }
    }

    public function actionGenerate($data)
    {

        $data = Utility::decrypt($data);
        $data = Json::decode($data);

        $employeeIds = $data['employeeId'];
        $month = $data['month'];
        $year = $data['year'];

        $models = [];
        $salaryModels = [];

        if (empty($employeeIds)) {
            $sql = "SELECT * FROM employee ORDER BY full_name";
            $sql2 = "SELECT employee_id, withdraw_amount as withdraw FROM salary_history WHERE month={$month} AND year={$year}";
        } else {
            $ids = implode(',', $employeeIds);
            $sql = "SELECT * FROM employee WHERE id IN ($ids) ORDER BY full_name";
            $sql2 = "SELECT employee_id, withdraw_amount as withdraw FROM salary_history WHERE month={$month} AND year={$year} AND employee_id IN ($ids)";
        }

        $records = Yii::$app->db->createCommand($sql2)->queryAll();
        foreach ($records as $record) {
            if (isset($salaryModels[$record['employee_id']])) {
                $salaryModels[$record['employee_id']]['withdraw'] += $record['withdraw'];
            } else {
                $salaryModels[$record['employee_id']]['withdraw'] = $record['withdraw'];
            }
        }

        $records = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($records as $record) {
            $withdraw = isset($salaryModels[$record['id']]['withdraw']) ? $salaryModels[$record['id']]['withdraw'] : 0;
            $models[] = [
                'name' => $record['full_name'],
                'salary' => (float)$record['salary'],
                'withdraw' => (float)$withdraw,
                'remaining' => (float)($record['salary'] - $withdraw)
            ];
        }

        $content = Yii::$app->controller->renderPartial('salarySheet', [
                'salaries' => $models,
                'month' => $month,
                'year' => $year,
            ]
        );


        $title = " # SALARY SHEET: " . CommonUtility::getMonthName($month) . ", " . $year;
        $filename = "salary_sheet_" . CommonUtility::getMonthName($month) . "_" . $year . '.pdf';

        $watermark = "SALARY SHEET";
        //$watermark = AppConfig::getStoreName();
        $destination = Pdf::DEST_BROWSER;
        $watermarkAlpha = 0.040;

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'defaultFont' => '@web/css/SourceSansPro-Regular.ttf',
            'format' => Pdf::FORMAT_A4,
            'filename' => $filename,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => $destination,
            'content' => $content,
            'cssInline' => file_get_contents(Yii::getAlias('@webroot/css/invoice.css')),
            'options' => ['title' => $title],
            'methods' => [
                'SetFooter' => ['Print at: ' . DateTimeUtility::getDate(null, SystemSettings::dateTimeFormat()) . '|Developed by: Axial Solution Ltd|Page: {PAGENO}|'],
            ]
        ]);

        $pdf->getApi()->SetWatermarkText($watermark);
        $pdf->getApi()->showWatermarkText = true;
        $pdf->getApi()->watermark_font = 'DejaVuSansCondensed';
        $pdf->getApi()->watermarkTextAlpha = $watermarkAlpha;
        $pdf->getApi()->SetDisplayMode('fullpage');

        if (SystemSettings::invoiceSalesAutoPrint()) {
            $pdf->getApi()->SetJS('this.print(true);');
        }

        $pdf->render();

    }

    public function actionSlip()
    {
        $data = Yii::$app->request->get();
        $this->redirect(['generate', 'data' => Utility::encrypt(Json::encode($data))]);
    }

    public function actionPayrollSlip()
    {

        $model = new SalaryHistory();
        if (Yii::$app->request->isPost) {
            //$model->load(Yii::$app->request->post());
            //return $this->generateSheet($model->month, $model->year, $model->employee_id);
        }

        return $this->render('payroll-slip', [
            'model' => $model,
        ]);

    }

    public function actionSalary()
    {
        $searchModel = new SalaryHistorySearch();
        $dataProvider = $searchModel->salary(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all SalaryHistory models.
     * @return mixed
     */
    public function actionAdvanceSalary()
    {
        $searchModel = new SalaryHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('advance-salary', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return array
     */
    public function actionCheckSalary()
    {
        if (Yii::$app->request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;

            $month = date('m');
            $year = date('Y');
            $customerId = Yii::$app->request->post('employeeId');

            if (Yii::$app->request->post('month')) {
                $month = Yii::$app->request->post('month');
            }

            if (Yii::$app->request->post('year')) {
                $year = Yii::$app->request->post('year');
            }

            $response = EmployeeUtility::getRemainingSalary($customerId, $month, $year);

            return ['success' => true, 'paid' => $response['paid'], 'salary' => $response['salary'], 'remaining' => $response['remaining']];
        }
    }

    /**
     * @param $id
     * @return string
     * @throws \Exception
     */
    public function actionUpdateWithdraw($id)
    {
        $model = $this->findModel($id);
        $type = $model->paymentType->type;
        $hasError = false;
        $expenseType = '';


        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            $newType = PaymentType::find()->where(['payment_type_id' => $model->payment_type])->one()->type;

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {

                if ($model->save()) {

                    if ($type == $newType) {

                        if ($type == PaymentType::TYPE_DEPOSIT) {

                            $expenseType = Expense::TYPE_DEPOSIT;

                            $deposit = DepositBook::find()->where(['reference_id' => $model->id, 'source' => DepositBook::SOURCE_EMPLOYEE_ADV_SALARY])->one();
                            $deposit->deposit_out = $model->withdraw_amount;
                            if (!$deposit->save()) {
                                $hasError = false;
                            }

                        } else {

                            $expenseType = Expense::TYPE_CASH;

                            $cash = CashBook::find()->where(['reference_id' => $model->id, 'source' => CashBook::SOURCE_EMPLOYEE_ADV_SALARY])->one();
                            $cash->cash_out = $model->withdraw_amount;
                            if ($cash->save()) {
                                $hasError = false;
                            }

                        }

                    } else {

                        if ($newType == PaymentType::TYPE_DEPOSIT) {

                            $expenseType = Expense::TYPE_DEPOSIT;

                            $cash = CashBook::find()->where(['reference_id' => $model->id, 'source' => CashBook::SOURCE_EMPLOYEE_ADV_SALARY])->one();
                            $cash->cash_out = 0;
                            $cash->remarks = 'Update ' . $cash->remarks;
                            if ($cash->save()) {

                                $deposit = new DepositBook();
                                $deposit->bank_id = $model->bank_id;
                                $deposit->branch_id = $model->branch_id;
                                $deposit->payment_type_id = $model->payment_type;
                                $deposit->ref_user_id = Yii::$app->user->getId();
                                $deposit->deposit_in = 0;
                                $deposit->deposit_out = $model->withdraw_amount;
                                $deposit->reference_id = $model->id;
                                $deposit->source = DepositBook::SOURCE_EMPLOYEE_ADV_SALARY;
                                $deposit->remarks = $model->remarks;
                                if (!$deposit->save()) {
                                    $hasError = true;
                                }
                            } else {
                                $hasError = true;
                            }

                        } else {

                            $expenseType = Expense::TYPE_CASH;

                            $deposit = DepositBook::find()->where(['reference_id' => $model->id, 'source' => CashBook::SOURCE_EMPLOYEE_ADV_SALARY])->one();

                            $deposit->deposit_out = 0;
                            $deposit->remarks = 'Update ' . $deposit->remarks;
                            if ($deposit->save()) {

                                $cash = new CashBook();
                                $cash->cash_in = 0;
                                $cash->cash_out = $model->withdraw_amount;
                                $cash->source = CashBook::SOURCE_EMPLOYEE_ADV_SALARY;
                                $cash->reference_id = $model->id;
                                $cash->remarks = $model->remarks;

                                if (!$cash->save()) {
                                    $hasError = true;
                                }
                            } else {
                                $hasError = true;
                            }
                        }
                    }

                    $expense = Expense::find()->where(['ref_id' => $model->id, 'expense_type_id' => ExpenseType::TYPE_EMPLOYEE_EXPENSE])->one();
                    $expense->expense_amount = $model->withdraw_amount;
                    $expense->type = $expenseType;
                    $expense->expense_remarks = $model->remarks;
                    if (!$expense->save()) {
                        $hasError = true;
                    }

                } else {
                    $hasError = true;
                }

                if ($hasError) {
                    $transaction->rollBack();
                } else {
                    $transaction->commit();
                }

            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }

            $this->redirect(['index']);

        }


        return $this->render('withdraw', [
            'model' => $model,
        ]);

    }

    /**
     * Creates a new SalaryHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionWithdraw()
    {
        $model = new SalaryHistory();
        $model->setScenario('daily');
        $model->user_id = Yii::$app->user->getId();
        $model->month = date('m');
        $model->year = date('Y');

        $hasError = false;
        $errorMessage = '';

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $totalRemaining = EmployeeUtility::getRemainingSalary($model->employee_id, date('m'), date('Y'));
            $model->remaining_salary = ($totalRemaining['remaining'] - $model->withdraw_amount);
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                if ($model->save()) {

                    $paymentType = PaymentType::findOne($model->payment_type);

                    if ($paymentType->type == PaymentType::TYPE_DEPOSIT) {

                        $deposit = new DepositBook();
                        $deposit->bank_id = $model->bank_id;
                        $deposit->branch_id = $model->branch_id;
                        $deposit->payment_type_id = $model->payment_type;
                        $deposit->ref_user_id = Yii::$app->user->getId();
                        $deposit->deposit_in = 0;
                        $deposit->deposit_out = $model->withdraw_amount;
                        $deposit->reference_id = $model->id;
                        $deposit->source = DepositBook::SOURCE_EMPLOYEE_ADV_SALARY;
                        $deposit->remarks = $model->remarks;
                        if ($deposit->save()) {

                            $expense = new Expense();
                            $expense->expense_type_id = ExpenseType::TYPE_EMPLOYEE_EXPENSE;
                            $expense->type = Expense::TYPE_DEPOSIT;
                            $expense->ref_id = $model->id;
                            $expense->user_id = $model->user_id;
                            $expense->expense_amount = $model->withdraw_amount;
                            $expense->expense_remarks = $model->remarks;
                            if (!$expense->save()) {
                                $hasError = true;
                            }
                        } else {
                            $hasError = true;
                        }

                    } else {

                        $cash = new CashBook();
                        $cash->cash_in = 0;
                        $cash->cash_out = $model->withdraw_amount;
                        $cash->source = CashBook::SOURCE_EMPLOYEE_ADV_SALARY;
                        $cash->reference_id = $model->id;
                        $cash->remarks = $model->remarks;

                        if ($cash->save()) {

                            $expense = new Expense();
                            $expense->expense_type_id = ExpenseType::TYPE_EMPLOYEE_EXPENSE;
                            $expense->type = Expense::TYPE_CASH;
                            $expense->ref_id = $model->id;
                            $expense->user_id = $model->user_id;
                            $expense->expense_amount = $model->withdraw_amount;
                            $expense->expense_remarks = $model->remarks;
                            if (!$expense->save()) {
                                $errorMessage = ["Model" => 'Expense', "Message" => $expense->getErrors()];
                                $hasError = true;
                            }
                        } else {
                            $errorMessage = ["Model" => 'Cash', "Message" => $cash->getErrors()];
                            $hasError = true;
                        }
                    }
                } else {
                    $errorMessage = ["Model" => 'SalaryHistory', "Message" => $model->getErrors()];
                    $hasError = true;
                }

                if ($hasError) {
                    CommonUtility::debug($errorMessage);
                    $transaction->rollBack();
                } else {
                    $transaction->commit();
                }


            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }

            return $this->redirect(['index']);
        }

        return $this->render('withdraw', [
            'model' => $model,
        ]);
    }

    public function actionApproved($id)
    {

        $response = [];
        $hasError = false;
        $model = $this->findModel(Utility::decrypt($id));
        if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
            $json = (object)Json::decode($model->extra);
            $model->bank_id = $json->bank_id;
            $model->branch_id = $json->branch_id;
        }


        $model->status = SalaryHistory::STATUS_APPROVED;
        $model->updated_by = Yii::$app->user->getId();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {


            if ($model->save()) {

                $expense = Expense::find()->where(['expense_type_id' => ExpenseType::TYPE_LC, 'ref_id' => $model->id])->one();

                if ($expense) {
                    $expense->ex = Expense::STATUS_APPROVED;
                    $expense->expense_amount = $model->withdraw_amount;
                    $expense->updated_by = Yii::$app->user->getId();
                } else {
                    $expense = new Expense();
                    $expense->extra = $model->extra;
                    $expense->source = Expense::SOURCE_EXTERNAL;
                    $expense->status = Expense::STATUS_APPROVED;
                    $expense->expense_type_id = ExpenseType::TYPE_SALARY;
                    $expense->ref_id = $model->id;
                    $expense->user_id = $model->user_id;
                    $expense->expense_amount = $model->withdraw_amount;
                    $expense->expense_remarks = $model->remarks;
                    $expense->type = $model->paymentType->type;
                }

                if ($expense->save()) {
                    if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                        $deposit = new DepositBook();
                        $deposit->bank_id = $model->bank_id;
                        $deposit->branch_id = $model->branch_id;
                        $deposit->payment_type_id = $model->payment_type;
                        $deposit->ref_user_id = Yii::$app->user->getId();
                        $deposit->deposit_in = 0;
                        $deposit->deposit_out = $model->withdraw_amount;
                        $deposit->reference_id = $model->id;
                        $deposit->source = DepositBook::SOURCE_ADVANCE_SALES;
                        $deposit->remarks = $model->remarks;
                        if (!$deposit->save()) {
                            $hasError = true;
                        }
                    } else {
                        $cash = new CashBook();
                        $cash->cash_in = 0;
                        $cash->cash_out = $model->withdraw_amount;
                        $cash->source = CashBook::SOURCE_LC;
                        $cash->ref_user_id = $model->user_id;
                        $cash->reference_id = $model->id;
                        $cash->source = CashBook::SOURCE_ADVANCE_SALES;
                        $cash->remarks = $model->remarks;
                        if (!$cash->save()) {
                            $hasError = true;
                        }
                    }
                } else {
                    $hasError = true;
                }
            }

            if ($hasError) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        if ($hasError) {
            $response = ['status' => 'Has error found', 'Error' => true];
        } else {
            $response = ['status' => 'Done', 'Error' => false];
        }

        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        } else {
            $message = "Amount # " . $model->withdraw_amount . " Employee: " . $model->employee->full_name . " has been approved.";
            FlashMessage::setMessage($message, "Approved Salary", "info");
            return $this->redirect(['index']);
        }

    }

    /**
     * Creates a new SalaryHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalaryHistory();
        $model->status = SalaryHistory::STATUS_PENDING;
        $model->extra = Json::encode(['bank_id' => 0, 'branch_id' => 0]);
        $model->user_id = Yii::$app->user->getId();
        $model->setScenario('monthlySalary');
        $addRules = false;

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());


            if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                if (empty($model->bank_id) || empty($model->branch_id)) {
                    $addRules = true;
                    $model->payment_type = 0;
                    $model->bank_id = 0;
                    $model->branch_id = 0;
                    $model->addError('bank_id', 'Bank Can\'t be Empty');
                    $model->addError('branch_id', 'Branch Can\'t be Empty');
                } else {
                    $model->extra = Json::encode(['bank_id' => $model->bank_id, 'branch_id' => $model->branch_id]);
                }
            }

            if ($addRules == false) {

                $amount = $model->remaining_salary;

                $totalPaid = ($model->withdraw_amount + $model->remaining_salary);
                $response = EmployeeUtility::getRemainingSalary($model->employee_id, $model->month, $model->year);

                if ($response['remaining'] == 0) {
                    $model->addError('remaining_salary', 'Already Paid Full Amount');
                } else if ($totalPaid > $response['salary']) {
                    $model->addError('remaining_salary', 'Payable amount (' . CommonUtility::getMonthName($model->month) . ', ' . $model->year . ') is: ' . $response['remaining']);
                } else {
                    $model->withdraw_amount = $model->remaining_salary;
                    $model->remaining_salary = $response['salary'] - $totalPaid;
                    if ($model->save()) {

                        FlashMessage::setMessage("New Payment Employee: " . $model->employee->full_name . " Amount #" . $amount . " has been created", "Employee Payment", "success");
                        if (Helper::checkRoute('approved')) {
                            return $this->redirect(['approved', 'id' => Utility::encrypt($model->id)]);
                        }
                        return $this->redirect(['advance-salary']);
                    }
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing SalaryHistory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Utility::decrypt($id));

        $model->status = SalaryHistory::STATUS_PENDING;
        $model->extra = Json::encode(['bank_id' => 0, 'branch_id' => 0]);
        $model->user_id = Yii::$app->user->getId();
        $model->setScenario('monthlySalary');
        $addRules = false;

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());


            if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                if (empty($model->bank_id) || empty($model->branch_id)) {
                    $addRules = true;
                    $model->payment_type = 0;
                    $model->bank_id = 0;
                    $model->branch_id = 0;
                    $model->addError('bank_id', 'Bank Can\'t be Empty');
                    $model->addError('branch_id', 'Branch Can\'t be Empty');
                } else {
                    $model->extra = Json::encode(['bank_id' => $model->bank_id, 'branch_id' => $model->branch_id]);
                }
            }

            if ($addRules == false) {

                $amount = $model->remaining_salary;

                $totalPaid = ($model->withdraw_amount + $model->remaining_salary);
                $response = EmployeeUtility::getRemainingSalary($model->employee_id, $model->month, $model->year);

                if ($response['remaining'] == 0) {
                    $model->addError('remaining_salary', 'Already Paid Full Amount');
                } else if ($totalPaid > $response['salary']) {
                    $model->addError('remaining_salary', 'Payable amount (' . CommonUtility::getMonthName($model->month) . ', ' . $model->year . ') is: ' . $response['remaining']);
                } else {
                    $model->withdraw_amount = $model->remaining_salary;
                    $model->remaining_salary = $response['salary'] - $totalPaid;
                    if ($model->save()) {

                        FlashMessage::setMessage("New Payment Employee: " . $model->employee->full_name . " Amount #" . $amount . " has been created", "Employee Payment", "success");
                        if (Helper::checkRoute('approved')) {
                            return $this->redirect(['approved', 'id' => Utility::encrypt($model->id)]);
                        }
                        return $this->redirect(['index']);
                    }
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);


    }

    /**
     * Finds the SalaryHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalaryHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalaryHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
