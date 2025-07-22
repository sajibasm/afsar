<?php

namespace app\services;

use app\components\DateTimeUtility;
use app\models\BankReconciliation;
use app\models\CashBook;
use app\models\ClientPaymentDetails;
use app\models\ClientPaymentHistory;
use app\models\DepositBook;
use app\models\PaymentType;
use app\models\ProductStatementOutlet;
use app\models\Sales;
use app\models\SalesDetails;
use Yii;
use yii\base\Component;
use yii\db\Exception;

class InvoiceDeleteService extends Component
{
    public function delete(Sales $model): array
    {
        $productStatementRows = [];

        $model->setUserAction("Sales Deleted");
        $salesDetails = SalesDetails::find()->where(['sales_id' => $model->sales_id])->all();
        $transaction = Yii::$app->db->beginTransaction();
        $userId = Yii::$app->user->getId();

        try {

            $oldModel = clone $model;

            $this->clearCashBook($model->sales_id);
            $this->clearDepositBook($model->sales_id);
            $this->clearBankReconciliation($model);


            // 2️⃣ Calculate Paid Amounts (old vs new)
            $paid = $model->paid_amount ?? 0;

            // 3️⃣ Determine Payment Modes (cash or bank)
            $isCash = $model->paymentTypeModel->type === PaymentType::TYPE_CASH;
            $isBank = $model->paymentTypeModel->type === PaymentType::TYPE_DEPOSIT;

            $cashAmount = $isCash ? $paid : 0;
            $bankAmount = $isBank ? $paid : 0;
            $salesAmount  = ($model->total_amount - $model->discount_amount) - $paid;

            if($oldModel->status===Sales::STATUS_APPROVED){
                ClientFinancialService::deleteSale(
                    $model->client_id,
                    $salesAmount,
                    $cashAmount,
                    $bankAmount,
                    ClientFinancialService::REF_TABLE_SALE,
                    $model->sales_id,
                    'Invoice deleted',
                    $userId
                );
            }


            $this->resetSalesModel($model);

            foreach ($salesDetails as $details) {
                $productStatementRows[] = $this->prepareProductStatementRow($details, $model);
                $this->markSalesDetailAsDeleted($details);
            }

            $this->batchInsertProductStatements($productStatementRows);
            $this->clearClientPayments($model->sales_id, $model->client_id);


            $transaction->commit();

            return Yii::$app->json->success('Invoice successfully deleted.');


        } catch (\Exception $e) {
            $transaction->rollBack();
            return Yii::$app->json->error('Invoice successfully deleted.', [
                'error' => $e->getMessage()
            ]);
        }
    }

    private function clearCashBook($salesId)
    {
        $cashBook = CashBook::findOne(['reference_id' => $salesId, 'source' => CashBook::SOURCE_SALES]);
        if ($cashBook) {
            $cashBook->cash_in = 0;
            $cashBook->remarks = "Invoice has been deleted";
            if (!$cashBook->save(false)) {
                throw new Exception("Unable to update CashBook record.");
            }
        }
    }

    private function clearDepositBook($salesId)
    {
        $depositBook = DepositBook::findOne(['reference_id' => $salesId, 'source' => DepositBook::SOURCE_SALES]);
        if ($depositBook) {
            $depositBook->deposit_in = 0;
            $depositBook->remarks = "Invoice has been deleted";
            if (!$depositBook->save(false)) {
                throw new Exception("Unable to update DepositBook record.");
            }
        }
    }

    private function clearBankReconciliation(Sales $model)
    {
        $bankReconciliation = BankReconciliation::findOne(['invoice_id' => $model->sales_id]);
        if ($bankReconciliation) {

            $oldApprovedAmount = $bankReconciliation->amount;

            $bankReconciliation->amount = 0;
            $bankReconciliation->approved_amount = 0;
            $bankReconciliation->remarks = "Invoice has been deleted";
            $bankReconciliation->status = BankReconciliation::STATUS_DELETE;
            if ($bankReconciliation->save(false)) {
                ClientFinancialService::adjustDueReconciliation(
                    $model->client_id,
                    - $oldApprovedAmount,
                    'Invoice Remove - Reversal of previous reconciliation (' . $oldApprovedAmount . ')',
                    Yii::$app->user->getId(),
                    ClientFinancialService::REF_TABLE_RECONCILIATION,
                    $bankReconciliation->id
                );
            }else{
                throw new Exception("Unable to update BankReconciliation.");
            }
        }
    }

    private function resetSalesModel(Sales $model)
    {
        $model->paid_amount = 0;
        $model->due_amount = 0;
        $model->discount_amount = 0;
        $model->received_amount = 0;
        $model->total_amount = 0;
        $model->reconciliation_amount = 0;
        $model->sales_return_amount = 0;
        $model->status = Sales::STATUS_DELETE;

        if (!$model->save(false)) {
            throw new Exception("Unable to update Sales record.");
        }
    }

    private function prepareProductStatementRow(SalesDetails $details, Sales $model): array
    {
        return [
            'item_id' => $details->item_id,
            'brand_id' => $details->brand_id,
            'size_id' => $details->size_id,
            'outlet_id' => $model->outletId,
            'quantity' => $details->quantity,
            'type' => ProductStatementOutlet::TYPE_SALES_DELETE,
            'remarks' => $model->remarks,
            'reference_id' => $model->sales_id,
            'user_id' => Yii::$app->user->id,
            'created_at' => DateTimeUtility::getDate(null, 'Y-m-d H:i:s'),
            'updated_at' => DateTimeUtility::getDate(null, 'Y-m-d H:i:s'),
        ];
    }

    private function markSalesDetailAsDeleted(SalesDetails $details)
    {
        $details->status = SalesDetails::STATUS_DELETE;
        if (!$details->save(false)) {
            throw new Exception("Unable to update SalesDetails for item ID {$details->item_id}.");
        }
    }

    private function batchInsertProductStatements(array $rows)
    {
        if (!empty($rows)) {
            $rowsInserted = Yii::$app->db->createCommand()->batchInsert(ProductStatementOutlet::tableName(), [
                'item_id', 'brand_id', 'size_id', 'outlet_id', 'quantity', 'type',
                'remarks', 'reference_id', 'user_id', 'created_at', 'updated_at'
            ], $rows)->execute();

            if ($rowsInserted !== count($rows)) {
                throw new Exception("Mismatch in ProductStatementOutlet rows inserted.");
            }
        }
    }

    private function clearClientPayments($salesId, $clientId)
    {

        $paymentHistory = ClientPaymentHistory::findOne([
            'sales_id' => $salesId,
            'client_id' => $clientId,
            'received_type'=>ClientPaymentHistory::RECEIVED_TYPE_SALES
        ]);

        if($paymentHistory){
            $paymentHistory->remaining_amount = 0;
            $paymentHistory->received_amount = 0;
            $paymentHistory->remarks = "Invoice has been deleted";
            $paymentHistory->status = ClientPaymentHistory::STATUS_DELETE;
            if($paymentHistory->save(false)) {
                $paymentDetails = ClientPaymentDetails::findOne(['payment_history_id' => $paymentHistory->client_payment_history_id]);
                if($paymentDetails){
                    $paymentDetails->paid_amount = 0;
                    if(!$paymentDetails->save(false)){
                        throw new Exception("Unable to update ClientPaymentDetails record.");
                    }
                }
            }else{
                throw new Exception("Unable to update ClientPaymentHistory record.");
            }
        }
    }
}
