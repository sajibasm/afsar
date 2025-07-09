<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\models\ClientPaymentHistory;
use app\models\ClientPaymentDetails;
use app\models\Sales;
use app\services\ClientFinancialService;

class PaymentSettlementService extends Component
{
    /**
     * Get due invoices for payment settlement.
     */
    private function getReceivables(ClientPaymentHistory $paymentHistory): array
    {
        return $paymentHistory->payType === ClientPaymentHistory::PAY_TYPE_MANUAL
            ? CustomerUtility::getDueInvoicesById($paymentHistory->invoices)
            : CustomerUtility::getDueInvoicesByCustomer($paymentHistory->client_id);
    }

    /**
     * Create ClientPaymentDetails record.
     */
    private function createPaymentDetail(ClientPaymentHistory $paymentHistory, Sales $sales, float $amount): ?ClientPaymentDetails
    {
        $netPayable = $sales->total_amount - $sales->discount_amount;
        $totalReceived = $sales->paid_amount + $sales->reconciliation_amount + $sales->sales_return_amount;
        $remainingDue = $netPayable - $totalReceived;

        $paymentType = ($amount >= $remainingDue)
            ? ClientPaymentDetails::PAYMENT_TYPE_FULL
            : ClientPaymentDetails::PAYMENT_TYPE_PARTIAL;

        $paymentDetail = new ClientPaymentDetails([
            'sales_id'           => $sales->sales_id,
            'client_id'          => $paymentHistory->client_id,
            'payment_history_id' => $paymentHistory->client_payment_history_id,
            'paid_amount'        => $amount,
            'payment_type'       => $paymentType,
        ]);

        return $paymentDetail->save() ? $paymentDetail : null;
    }

    /**
     * Update the sales record after payment settlement.
     */
    private function updateSales(Sales $sales, ClientPaymentHistory $paymentHistory, float $amount): bool
    {
        if($paymentHistory->received_type===ClientPaymentHistory::RECEIVED_TYPE_RECONCILIATION){
            $sales->reconciliation_amount+=$amount;
        }else{
            $sales->received_amount += $amount;
            $sales->paid_amount += $amount;
            $sales->due_amount -= $amount;
        }
        return $sales->save();
    }

    /**
     * Log the financial transaction related to this payment settlement.
     */
    private function logFinancialTransaction(ClientPaymentHistory $paymentHistory, float $adjustableAmount, int $paymentDetailId): void
    {
        ClientFinancialService::logTransaction(
            $paymentHistory->client_id,
            $paymentHistory->received_type===ClientPaymentHistory::RECEIVED_TYPE_RECONCILIATION ? ClientFinancialService::TYPE_RECONCILIATION : ClientFinancialService::TYPE_PAYMENT,
            ClientFinancialService::MODE_ADJUSTED,
            -$adjustableAmount,
            ClientFinancialService::REF_TABLE_PAYMENT_SETTLEMENT,
            $paymentDetailId,   // ✅ Using payment_details_id here
            $paymentHistory->remarks,
            Yii::$app->user->getId()
        );
    }

    /**
     * Settle customer payments against outstanding invoices and log financial transactions.
     *
     * @param ClientPaymentHistory $paymentHistory
     * @return bool
     * @throws \Throwable
     */
    public function settlePayment(ClientPaymentHistory $paymentHistory): bool
    {
        $availableBalance = $paymentHistory->remaining_amount;
        $receivables = $this->getReceivables($paymentHistory);


        if (empty($receivables)) {
            return true; // No invoices to settle
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($receivables as $receivable) {
                if ($availableBalance <= 0) {
                    break;
                }

                $adjustableAmount = min($availableBalance, $receivable->due);
                $sales = Sales::findOne($receivable->sales_id);

                if (!$sales) {
                    continue;
                }

                $paymentDetail = $this->createPaymentDetail($paymentHistory, $sales, $adjustableAmount);

                if (!$paymentDetail || !$this->updateSales($sales, $paymentHistory, $adjustableAmount)) {
                    $transaction->rollBack();
                    return false;
                }

                $this->logFinancialTransaction($paymentHistory, $adjustableAmount, $paymentDetail->client_payment_details_id);

                $availableBalance -= $adjustableAmount;
            }


            $paymentHistory->remaining_amount = $availableBalance;

            if (!$paymentHistory->save()) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return true;

        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
