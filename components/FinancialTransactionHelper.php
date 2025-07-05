<?php

namespace app\components;

use Yii;
use yii\db\Expression;
use app\services\CustomerFinancialService;
use app\models\CustomerTransactionSummary;

class FinancialTransactionHelper
{
    /**
     * Handle invoice creation, update or difference adjustment.
     */
    public static function processInvoiceDifference($customerId, $oldAmount, $newAmount, $referenceId, $referenceModel, $remarks = null, $userId = null)
    {
        $difference = $newAmount - $oldAmount;

        if ($difference == 0) {
            return;
        }

        $action = ($difference > 0) ? 'add' : 'subtract';

        CustomerFinancialService::recordTransaction(
            $customerId,
            CustomerTransactionSummary::TYPE_PURCHASE,
            abs($difference),
            $action,
            $remarks ?? "Invoice updated (ID: {$referenceId})",
            $userId ?? Yii::$app->user->id,
            $referenceId,
            $referenceModel
        );
    }

    /**
     * Reverse entire invoice amount (on delete).
     */
    public static function reverseInvoice($customerId, $amount, $referenceId, $referenceModel, $remarks = null, $userId = null)
    {
        CustomerFinancialService::recordTransaction(
            $customerId,
            CustomerTransactionSummary::TYPE_PURCHASE,
            $amount,
            'subtract',
            $remarks ?? "Invoice deleted (ID: {$referenceId})",
            $userId ?? Yii::$app->user->id,
            $referenceId,
            $referenceModel
        );
    }

    /**
     * Handle payment creation, update or deletion (cash or bank).
     */
    public static function recordPayment($customerId, $oldAmount, $newAmount, $paymentType, $referenceId, $referenceModel, $remarks = null, $userId = null)
    {
        if (!in_array($paymentType, [
            CustomerTransactionSummary::TYPE_CASH_RECEIVED,
            CustomerTransactionSummary::TYPE_BANK_RECEIVED
        ])) {
            throw new \InvalidArgumentException('Invalid payment type');
        }

        $difference = $newAmount - $oldAmount;

        if ($difference == 0) {
            return;
        }

        $action = ($difference > 0) ? 'add' : 'subtract';
        CustomerFinancialService::recordTransaction(
            $customerId,
            $paymentType,
            abs($difference),
            $action,
            $remarks ?? "Payment update for Ref #{$referenceId}",
            $userId ?? Yii::$app->user->id,
            $referenceId,
            $referenceModel
        );
    }

    /**
     * Handle return creation, update, or deletion.
     */
    public static function recordReturn($customerId, $oldAmount, $newAmount, $returnType, $referenceId, $referenceModel, $remarks = null, $userId = null)
    {
        if (!in_array($returnType, [
            CustomerTransactionSummary::TYPE_SALES_RETURN,
            CustomerTransactionSummary::TYPE_CASH_RETURN,
            CustomerTransactionSummary::TYPE_BANK_RETURN
        ])) {
            throw new \InvalidArgumentException('Invalid return type');
        }

        $difference = $newAmount - $oldAmount;

        if ($difference == 0) {
            return;
        }

        $action = ($difference > 0) ? 'add' : 'subtract';

        CustomerFinancialService::recordTransaction(
            $customerId,
            $returnType,
            abs($difference),
            $action,
            $remarks ?? "Return updated for Ref #{$referenceId}",
            $userId ?? Yii::$app->user->id,
            $referenceId,
            $referenceModel
        );
    }

    /**
     * Handle reconciliation creation, update or deletion.
     */
    public static function recordReconciliation($customerId, $oldAmount, $newAmount, $referenceId, $referenceModel, $remarks = null, $userId = null)
    {
        $difference = $newAmount - $oldAmount;

        if ($difference == 0) {
            return;
        }

        $action = ($difference > 0) ? 'add' : 'subtract';

        CustomerFinancialService::recordTransaction(
            $customerId,
            CustomerTransactionSummary::TYPE_RECONCILIATION,
            abs($difference),
            $action,
            $remarks ?? "Reconciliation update",
            $userId ?? Yii::$app->user->id,
            $referenceId,
            $referenceModel
        );
    }
}
