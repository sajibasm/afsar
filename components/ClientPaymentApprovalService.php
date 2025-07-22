<?php

namespace app\components;

use app\services\ClientFinancialService;
use Yii;
use yii\base\Component;
use app\models\ClientPaymentHistory;
use app\models\CashBook;
use app\models\DepositBook;
use app\models\PaymentType;
use yii\helpers\Json;

class ClientPaymentApprovalService extends Component
{
    public function approve($encryptedId)
    {
        $id = Utility::decrypt($encryptedId);
        $model = ClientPaymentHistory::findOne($id);

        if (!$model) {
            return Yii::$app->json->error('Invalid payment ID.');
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {

            $oldModel = clone $model;

            $model->status = ClientPaymentHistory::STATUS_APPROVED;
            $model->approved_amount = $model->received_amount;
            $model->approved_received_type = $model->received_type;
            $model->approved_payment_type_id = $model->payment_type_id;
            $model->updated_by = Yii::$app->user->id;

            if (!$model->save()) {
                $transaction->rollBack();
                return Yii::$app->json->error('Payment update failed', ['error' => $model->getErrors()]);
            }

            $result = $this->processPayment($model, $oldModel);

            if ($result['error']) {
                $transaction->rollBack();
                return Yii::$app->json->error($result['message']);
            }

            $transaction->commit();
            return Yii::$app->json->success('Payment approved successfully.');
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return Yii::$app->json->error('Unable to approve payment.', ['error' => $e->getMessage()]);
        }
    }

    private function determinePaymentSource($type)
    {
        if ($type == ClientPaymentHistory::RECEIVED_TYPE_DUE_RECEIVED) {
            return [
                'cash' => CashBook::SOURCE_DUE_RECEIVED,
                'deposit' => DepositBook::SOURCE_DUE_RECEIVED,
            ];
        }

        if ($type == ClientPaymentHistory::RECEIVED_TYPE_RECONCILIATION) {
            return [
                'cash' => CashBook::SOURCE_RECONCILIATION_RECEIVED,
                'deposit' => DepositBook::SOURCE_RECONCILIATION_RECEIVED,
            ];
        }

        return [
            'cash' => CashBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED,
            'deposit' => DepositBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED,
        ];
    }

    private function processOldPayment(ClientPaymentHistory $model)
    {
        // Skip if no approved state to reverse
        if (empty($model->approved_received_type) || $model->approved_amount === null) {
            return ['error' => false, 'message' => 'No approved payment found to reverse.'];
        }

        $paymentType = $model->approvedPaymentType->type ?? null;
        $paymentSource = $this->determinePaymentSource($model->approved_received_type);

        if ($model->approved_received_type == ClientPaymentHistory::RECEIVED_TYPE_RECONCILIATION) {
            ClientFinancialService::adjustDueReconciliation(
                $model->client_id,
                -$model->approved_amount,
                "Payment Invoice is Modified",
                Yii::$app->user->id,
                ClientFinancialService::REF_TABLE_PAYMENT_RECONCILIATION,
                $model->client_payment_history_id
            );
            return ['error' => false];
        }

        if ($paymentType == PaymentType::TYPE_CASH) {
            $cashBook = CashBook::findOne([
                'source' => $paymentSource['cash'],
                'reference_id' => $model->client_payment_history_id,
            ]);

            if (!$cashBook) {
                return ['error' => true, 'message' => 'Previous CashBook record not found.'];
            }

            if (!$cashBook->delete()) {
                return ['error' => true, 'message' => 'Failed to delete previous CashBook record.'];
            }

            ClientFinancialService::recordStandalonePayment(
                $model->client_id,
                -$model->approved_amount,
                ClientFinancialService::MODE_CASH,
                ClientFinancialService::REF_TABLE_PAYMENT,
                $model->client_payment_history_id,
                "Payment Invoice is Modified",
                Yii::$app->user->id,
            );

        } else {
            $depositBook = DepositBook::findOne([
                'source' => $paymentSource['deposit'],
                'reference_id' => $model->client_payment_history_id,
            ]);

            if (!$depositBook) {
                return ['error' => true, 'message' => 'Previous DepositBook record not found.'];
            }

            if (!$depositBook->delete()) {
                return ['error' => true, 'message' => 'Failed to delete previous DepositBook record.'];
            }

            ClientFinancialService::recordStandalonePayment(
                $model->client_id,
                -$model->approved_amount,
                ClientFinancialService::MODE_BANK,
                ClientFinancialService::REF_TABLE_PAYMENT,
                $model->client_payment_history_id,
                "Payment Invoice is Modified",
                Yii::$app->user->id,
            );
        }

        return ['error' => false];
    }


    private function processPayment(ClientPaymentHistory $model, ClientPaymentHistory $oldModel)
    {
        $paymentType = $model->paymentType->type ?? null;
        $paymentSource = $this->determinePaymentSource($model->received_type);

        $oldResult = $this->processOldPayment($oldModel);

        if ($oldResult['error']) {
            return $oldResult;  // Stop and return the error from old payment reversal
        }

        if ($model->received_type == ClientPaymentHistory::RECEIVED_TYPE_RECONCILIATION) {
            ClientFinancialService::adjustDueReconciliation(
                $model->client_id,
                $model->received_amount,
                $model->remarks,
                Yii::$app->user->id,
                ClientFinancialService::REF_TABLE_PAYMENT_RECONCILIATION,
                $model->client_payment_history_id
            );
            return ['error' => false];
        }

        if ($paymentType == PaymentType::TYPE_CASH) {
            return $this->saveCashBook($model, $model->received_amount, $paymentSource['cash']);
        } else {
            return $this->saveDepositBook($model, $model->received_amount, $paymentSource['deposit']);
        }
    }


    private function saveCashBook(ClientPaymentHistory $model, $amount, $source = CashBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED)
    {
        $model->remarks = !empty($model->remarks) ? $model->remarks : 'Payment Approved';
        $cashBook = new CashBook([
            'outletId'     => $model->outletId,
            'cash_in'      => $amount,
            'cash_out'     => 0,
            'source'       => $source,
            'reference_id' => $model->client_payment_history_id,
            'ref_user_id' => $model->user_id,
            'remarks'      => $model->remarks,
        ]);

        if ($cashBook->save()) {
            ClientFinancialService::recordStandalonePayment(
                $model->client_id,
                $amount,  // Use approved amount
                ClientFinancialService::MODE_CASH,
                ClientFinancialService::REF_TABLE_PAYMENT,
                $model->client_payment_history_id,
                $model->remarks,
                Yii::$app->user->id,
            );

            return ['error' => false];
        }

        return [
            'error' => true,
            'message' => 'CashBook save failed.',
            'details' => $cashBook->getErrors(),
        ];
    }

    private function saveDepositBook(ClientPaymentHistory $model, $amount, $source = DepositBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED)
    {
        $model->remarks = !empty($model->remarks) ? $model->remarks : 'Payment Approved';
        $json = Json::decode($model->extra);
        $depositBook = new DepositBook([
            'outletId'       => $model->outletId,
            'ref_user_id'    => $model->user_id,
            'reference_id' => $model->client_payment_history_id,
            'payment_type_id'=> $model->payment_type_id,
            'bank_id'        => $json['bank_id'] ?? null,
            'branch_id'      => $json['branch_id'] ?? null,
            'deposit_in'     => $amount,
            'deposit_out'    => 0,
            'source'         => $source,
            'remarks'        => $model->remarks,
        ]);

        if ($depositBook->save()) {
            ClientFinancialService::recordStandalonePayment(
                $model->client_id,
                $amount,  // Use approved amount
                ClientFinancialService::MODE_BANK,
                ClientFinancialService::REF_TABLE_PAYMENT,
                $model->client_payment_history_id,
                $model->remarks,
                Yii::$app->user->id,
            );
            return ['error' => false];
        }

        return [
            'error' => true,
            'message' => 'DepositBook save failed.',
            'details' => $depositBook->getErrors(),
        ];
    }
}
