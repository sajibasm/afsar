<?php

namespace app\services;

use Yii;
use app\models\ClientFinancialSummary;
use app\models\ClientTransactionSummary;

class ClientFinancialService
{
    // Transaction Types
    const TYPE_SALE = 'Sale';
    const TYPE_PAYMENT = 'Payment';
    const TYPE_RETURN = 'Return';
    const TYPE_ADJUSTMENT = 'Adjustment';
    const TYPE_RECONCILIATION = 'Reconciliation';


    // Transaction Modes
    const MODE_CASH = 'Cash';
    const MODE_BANK = 'Bank';
    const MODE_ADJUSTED = 'Adjusted';
    const MODE_MIXED = 'Mixed';

    // Reference Tables
    const REF_TABLE_SALE = 'Sales';
    const REF_TABLE_PAYMENT = 'Payment';
    const REF_TABLE_PAYMENT_SETTLEMENT = 'Payment Settlement';
    const REF_TABLE_PAYMENT_RECONCILIATION = 'Payment Reconciliation';
    const REF_TABLE_SALES_RETURN = 'Sales Return';
    const REF_TABLE_RECONCILIATION = 'Reconciliation';

    protected static function getOrCreateSummary($clientId)
    {
        $summary = ClientFinancialSummary::findOne(['client_id' => $clientId]);
        if (!$summary) {
            $summary = new ClientFinancialSummary([
                'client_id' => $clientId,
                'total_cash_received' => 0,
                'total_bank_received' => 0,
                'total_cash_returned' => 0,
                'total_bank_returned' => 0,
                'total_reconciled' => 0,
                'total_due' => 0,
            ]);
            $summary->save(false);
        }
        return $summary;
    }

    public static function logTransaction($clientId, $type, $mode, $amount, $refTable = null, $refId = null, $remarks = null, $userId = null)
    {
        $txn = new ClientTransactionSummary();
        $txn->client_id = $clientId;
        $txn->transaction_type = $type;
        $txn->transaction_mode = $mode;
        $txn->transaction_amount = $amount;
        $txn->reference_table = $refTable;
        $txn->reference_id = $refId;
        $txn->remarks = $remarks;
        $txn->created_by = $userId ?? Yii::$app->user->id;
        $txn->save(false);
    }

    /**
     * Record a new sale with optional payment (cash, bank, or both).
     *
     * ✅ Use Cases:
     * 1. Full payment → cash or bank → no due remains.
     * ClientFinancialService::recordSaleWithOptionalPayment(5, 1000, 1000, 0, self::REF_TABLE_SALE, 101, 'Full cash sale');
     *
     * 2. Partial payment → some due remains.
     * ClientFinancialService::recordSaleWithOptionalPayment(5, 1000, 500, 0, self::REF_TABLE_SALE, 102, 'Half cash sale');
     *
     * 3. Mixed payment (cash + bank) → partial or full.
     * ClientFinancialService::recordSaleWithOptionalPayment(5, 1000, 400, 600, self::REF_TABLE_SALE, 103, 'Mixed payment sale');
     */
    public static function recordSaleWithOptionalPayment($clientId, $saleAmount, $cash = 0, $bank = 0, $refTable = null, $refId = null, $remarks = null, $userId = null)
    {
        $summary = self::getOrCreateSummary($clientId);
        $summary->total_due += $saleAmount;
        $summary->total_cash_received += $cash;
        $summary->total_bank_received += $bank;
        $summary->total_due -= ($cash + $bank);
        if ($summary->total_due < 0) $summary->total_due = 0;
        $summary->save(false);

        self::logTransaction($clientId, self::TYPE_SALE, self::MODE_ADJUSTED, $saleAmount, $refTable, $refId, $remarks, $userId);
        if ($cash > 0) {
            self::logTransaction($clientId, self::TYPE_PAYMENT, self::MODE_CASH, -$cash, $refTable, $refId, 'Payment (Cash)', $userId);
        }
        if ($bank > 0) {
            self::logTransaction($clientId, self::TYPE_PAYMENT, self::MODE_BANK, -$bank, $refTable, $refId, 'Payment (Bank)', $userId);
        }
    }

    /**
     * Update an approved sale (increase/decrease sale amount).
     *
     * ✅ Use Cases:
     * 1. Sale increased → due increases.
     * ClientFinancialService::updateSale(5, 1000, 1200, self::REF_TABLE_SALE, 201, 'Price increased');
     *
     * 2. Sale reduced → due decreases.
     * ClientFinancialService::updateSale(5, 1000, 800, self::REF_TABLE_SALE, 202, 'Price reduced');
     */

    public static function updateSaleWithOptionalPayment($clientId, $oldSaleAmount, $newSaleAmount, $oldCash = 0, $newCash = 0, $oldBank = 0, $newBank = 0, $refTable = null, $refId = null, $remarks = null, $userId = null)
    {
        $summary = self::getOrCreateSummary($clientId);

        // 1️⃣ Adjust due based on sale amount difference
        $saleDifference = $newSaleAmount - $oldSaleAmount;
        $summary->total_due += $saleDifference;

        // 2️⃣ Adjust cash received difference
        $cashDifference = $newCash - $oldCash;
        $summary->total_cash_received += $cashDifference;
        $summary->total_due -= $cashDifference;

        // 3️⃣ Adjust bank received difference
        $bankDifference = $newBank - $oldBank;
        $summary->total_bank_received += $bankDifference;
        $summary->total_due -= $bankDifference;

        // 4️⃣ Ensure no negative due
        if ($summary->total_due < 0) {
            $summary->total_due = 0;
        }

        $summary->save(false);

        // 5️⃣ Log sale adjustment if sale amount changed
        if ($saleDifference != 0) {
            self::logTransaction($clientId, self::TYPE_ADJUSTMENT, self::MODE_ADJUSTED, $saleDifference, $refTable, $refId, $remarks ?? 'Sale amount updated', $userId);
        }

        // 6️⃣ Log payment adjustments
        if ($cashDifference != 0) {
            self::logTransaction($clientId, self::TYPE_PAYMENT, self::MODE_CASH, $cashDifference, $refTable, $refId, 'Cash payment updated', $userId);
        }
        if ($bankDifference != 0) {
            self::logTransaction($clientId, self::TYPE_PAYMENT, self::MODE_BANK, $bankDifference, $refTable, $refId, 'Bank payment updated', $userId);
        }
    }

    /**
     * Delete a sale → adjust customer financials by reducing due, cash, and bank amounts.
     *
     * ✅ Use Case Example:
     * ClientFinancialService::deleteSale(
     *     $clientId,       // e.g., 5
     *     $saleAmount,     // e.g., 1000
     *     $cashPaid,       // e.g., 500
     *     $bankPaid,       // e.g., 0
     *     ClientFinancialService::REF_TABLE_SALE,
     *     $salesId,        // e.g., 301
     *     'Sale deleted',
     *     $userId
     * );
     */
    public static function deleteSale($clientId, $saleAmount, $cash = 0, $bank = 0, $refTable, $refId, $remarks = null, $userId = null)
    {
        if ($cash > 0) {
            self::logTransaction($clientId, self::TYPE_ADJUSTMENT, self::MODE_CASH, $cash, $refTable, $refId, 'Payment (Cash)', $userId);
        }
        if ($bank > 0) {
            self::logTransaction($clientId, self::TYPE_ADJUSTMENT, self::MODE_BANK, $bank, $refTable, $refId, 'Payment (Bank)', $userId);
        }

        $summary = self::getOrCreateSummary($clientId);
        $summary->total_due -= $saleAmount;
        $summary->total_cash_received -= $cash;
        $summary->total_bank_received -= $bank;
        if ($summary->total_due < 0) $summary->total_due = 0;
        $summary->save(false);
        self::logTransaction($clientId, self::TYPE_ADJUSTMENT, self::MODE_ADJUSTED, -($saleAmount+$cash+$bank), $refTable, $refId, $remarks, $userId);
    }

    /**
     * Record payment without a new sale (for clearing old dues).
     *
     * ✅ Use Cases:
     * 1. Cash payment → due reduced.
     * ClientFinancialService::recordStandalonePayment(5, 500, self::MODE_CASH, self::REF_TABLE_PAYMENT, 401, 'Late cash payment');
     *
     * 2. Bank payment → due reduced.
     * ClientFinancialService::recordStandalonePayment(5, 500, self::MODE_BANK, self::REF_TABLE_PAYMENT, 402, 'Late bank payment');
     */
    public static function recordStandalonePayment($clientId, $amount, $mode, $refTable = null, $refId = null, $remarks = null, $userId = null)
    {
        $summary = self::getOrCreateSummary($clientId);
        if ($mode === self::MODE_CASH) {
            $summary->total_cash_received += $amount;
        } elseif ($mode === self::MODE_BANK) {
            $summary->total_bank_received += $amount;
        }
        $summary->total_due -= $amount;
        if ($summary->total_due < 0) $summary->total_due = 0;
        $summary->save(false);

        self::logTransaction($clientId, self::TYPE_PAYMENT, $mode, -$amount, $refTable, $refId, $remarks, $userId);
    }

    /**
     * Record a return (refund to customer or due reduction).
     *
     * ✅ Use Cases:
     * 1. Cash refund → money back to customer.
     * ClientFinancialService::recordSalesReturn(5, 200, self::MODE_CASH, self::REF_TABLE_SALES_RETURN, 501, 'Refunded cash');
     *
     * 2. Bank refund → customer gets money back via bank.
     * ClientFinancialService::recordSalesReturn(5, 200, self::MODE_BANK, self::REF_TABLE_SALES_RETURN, 502, 'Refunded bank');
     *
     * 3. Return adjusted against due → no cash/bank.
     * ClientFinancialService::recordSalesReturn(5, 200, self::MODE_ADJUSTED, self::REF_TABLE_SALES_RETURN, 503, 'Return adjusted');
     */
    public static function recordSalesReturn($clientId, $amount, $mode, $refTable = null, $refId = null, $remarks = null, $userId = null)
    {
        $summary = self::getOrCreateSummary($clientId);
        if ($mode === self::MODE_CASH) {
            $summary->total_cash_returned += $amount;
        } elseif ($mode === self::MODE_BANK) {
            $summary->total_bank_returned += $amount;
        } elseif ($mode === self::MODE_ADJUSTED) {
            $summary->total_due -= $amount;
            if ($summary->total_due < 0) $summary->total_due = 0;
        }
        $summary->save(false);

        self::logTransaction($clientId, self::TYPE_RETURN, $mode, $amount, $refTable, $refId, $remarks, $userId);
    }

    /**
     * Manually reconcile outstanding due (write-off, correction, dispute resolution).
     *
     * ✅ Use Cases:
     * 1. Write-off bad debt.
     * ClientFinancialService::adjustDueReconciliation(5, 150, 'Debt write-off', Yii::$app->user->id, self::REF_TABLE_RECONCILIATION, 601);
     *
     * 2. Adjustment due to overpayment.
     * ClientFinancialService::adjustDueReconciliation(5, 50, 'Adjustment for overpayment', Yii::$app->user->id, self::REF_TABLE_RECONCILIATION, 602);
     *
     * 3. Dispute resolution reducing customer due.
     * ClientFinancialService::adjustDueReconciliation(5, 75, 'Customer dispute settlement', Yii::$app->user->id, self::REF_TABLE_RECONCILIATION, 603');
     */
    public static function adjustDueReconciliation($clientId, $adjustAmount, $remarks = null, $userId = null, $refTable = null, $refId = null)
    {
        $summary = self::getOrCreateSummary($clientId);
        $summary->total_due -= $adjustAmount;
        if ($summary->total_due < 0) $summary->total_due = 0;
        $summary->total_reconciled += $adjustAmount;
        $summary->save(false);

        self::logTransaction($clientId, self::TYPE_ADJUSTMENT, self::MODE_ADJUSTED, -$adjustAmount, $refTable, $refId, $remarks, $userId, $adjustAmount);
    }
}
