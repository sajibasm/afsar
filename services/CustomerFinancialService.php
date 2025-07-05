<?php

namespace app\services;

use Yii;
use yii\db\Expression;
use app\models\CustomerFinancialSummary;
use app\models\CustomerTransactionSummary;
use app\models\CustomerTransactionLogs;

class CustomerFinancialService
{
    public static function recordTransaction(
        $customerId,
        $transactionType,
        $amount,
        $action,
        $remarks = null,
        $userId = null,
        $referenceId = null,
        $referenceModel = null
    ) {
        if (!in_array($transactionType, CustomerTransactionSummary::TRANSACTION_TYPES, true)) {
            throw new \InvalidArgumentException("Invalid transaction type: {$transactionType}");
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // 1️⃣ Update Summary
            $summary = CustomerTransactionSummary::findOne([
                'customer_id' => $customerId,
                'transaction_type' => $transactionType
            ]);

            if (!$summary) {
                $summary = new CustomerTransactionSummary();
                $summary->customer_id = $customerId;
                $summary->transaction_type = $transactionType;
                $summary->total_amount = 0;
            }

            switch ($action) {
                case 'add':
                    $summary->total_amount += $amount;
                    break;
                case 'subtract':
                    $summary->total_amount -= $amount;
                    break;
                case 'adjust':
                    $summary->total_amount = $amount;
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid action type: {$action}");
            }

            $summary->reference_id = $referenceId;
            $summary->reference_model = $referenceModel;
            $summary->last_transaction_date = new Expression('NOW()');
            $summary->last_updated_by = $userId;
            $summary->last_updated = new Expression('NOW()');
            $summary->save(false);

            // 2️⃣ Update Payment Status if Purchase
            if ($transactionType === CustomerTransactionSummary::TYPE_PURCHASE) {
                self::updatePaymentStatus($customerId, $summary);
            }

            // 3️⃣ Update Financial Summary
            self::updateFinancialSummary($customerId, $transactionType, $amount, $action);

            // 4️⃣ Insert Log
            $log = new CustomerTransactionLogs();
            $log->customer_id = $customerId;
            $log->transaction_type = $transactionType;
            $log->amount = $amount;
            $log->action = $action;
            $log->remarks = $remarks;
            $log->reference_id = $referenceId;
            $log->reference_model = $referenceModel;
            $log->performed_by = $userId;
            $log->performed_at = new Expression('NOW()');
            $log->save(false);

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Transaction failed: " . $e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    private static function updatePaymentStatus($customerId, $summary)
    {
        $cash = CustomerTransactionSummary::findOne([
            'customer_id' => $customerId,
            'transaction_type' => CustomerTransactionSummary::TYPE_CASH_RECEIVED
        ])->total_amount ?? 0;

        $bank = CustomerTransactionSummary::findOne([
            'customer_id' => $customerId,
            'transaction_type' => CustomerTransactionSummary::TYPE_BANK_RECEIVED
        ])->total_amount ?? 0;

        $totalPaid = $cash + $bank;
        $due = $summary->total_amount - $totalPaid;

        if ($due <= 0) {
            $summary->payment_status = 'fully_paid';
        } elseif ($totalPaid > 0) {
            $summary->payment_status = 'partially_paid';
        } else {
            $summary->payment_status = 'due';
        }

        $summary->save(false);
    }

    private static function updateFinancialSummary($customerId, $transactionType, $amount, $action)
    {
        $summary = CustomerFinancialSummary::findOne(['customer_id' => $customerId]);
        if (!$summary) {
            $summary = new CustomerFinancialSummary();
            $summary->customer_id = $customerId;
            $summary->total_dues = 0;
            $summary->total_discount = 0;
        }

        if ($transactionType === CustomerTransactionSummary::TYPE_PURCHASE) {
            $summary->total_dues += ($action === 'add') ? $amount : -$amount;
        } elseif (in_array($transactionType, [
            CustomerTransactionSummary::TYPE_CASH_RECEIVED,
            CustomerTransactionSummary::TYPE_BANK_RECEIVED
        ])) {
            $summary->total_dues -= ($action === 'add') ? $amount : -$amount;
        }

        $summary->last_updated = new Expression('NOW()');
        $summary->save(false);
    }
}
