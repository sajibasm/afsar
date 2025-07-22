<?php
namespace app\services;
use app\models\CashBook;
use app\models\ClientPaymentDetails;
use app\models\ClientPaymentHistory;
use app\models\DepositBook;
use app\models\PaymentType;
use app\models\ProductStatementOutlet;
use app\models\Sales;
use app\models\SalesDetails;
use Yii;
use yii\db\Exception;

class InvoiceApproveService
{
    public function approve(Sales $model): array
    {
        try {
            $oldSales = clone $model;

            $type = ($model->type == Sales::TYPE_SALES_UPDATE) ? Sales::TYPE_SALES_UPDATE : Sales::TYPE_SALES;

            return $this->saveSales($model, $type, $oldSales);

        }catch (Exception $e){

            return ['success' => true, 'message' => $e->getMessage()];
        }
    }

    protected static function getOrCreatePaymentDetails($clientId, $salesId)
    {
        $paymentDetails = ClientPaymentDetails::findOne(['sales_id' =>$salesId, 'client_id' => $clientId]);
        if (!$paymentDetails) {
            $paymentDetails = new ClientPaymentDetails([
                'sales_id' => $salesId,
                'client_id' => $clientId,
            ]);
            $paymentDetails->save(false);
        }
        return $paymentDetails;
    }

    protected static function getOrCreatePaymentHistory($clientId, $salesId)
    {
        $paymentHistory = ClientPaymentHistory::findOne(['sales_id' =>$salesId, 'client_id' => $clientId]);
        if (!$paymentHistory) {
            $paymentHistory = new ClientPaymentHistory([
                'sales_id' => $salesId,
                'client_id' => $clientId,
            ]);
            $paymentHistory->save(false);
        }
        return $paymentHistory;
    }

    private function processPaymentDetailsAndHistory(Sales $model, $type)
    {
        if ($model->paid_amount > 0) {
            // Payment with actual amount
            $paymentHistory = self::getOrCreatePaymentHistory($model->client_id, $model->sales_id);
            $paymentHistory->outletId = $model->outletId;
            $paymentHistory->user_id = $model->user_id;
            $paymentHistory->received_amount = $model->paid_amount;
            $paymentHistory->remaining_amount = 0;
            $paymentHistory->remarks = $model->remarks;
            $paymentHistory->received_type = ClientPaymentHistory::RECEIVED_TYPE_SALES;
            $paymentHistory->status = ClientPaymentHistory::STATUS_APPROVED;
            $paymentHistory->updated_by = $model->user_id;

            if ($paymentHistory->save(false)) {
                $paymentDetails = self::getOrCreatePaymentDetails($model->client_id, $model->sales_id);
                $paymentDetails->payment_history_id = $paymentHistory->client_payment_history_id;  // ✅ FIX: use correct PK
                $paymentDetails->paid_amount = $model->paid_amount;
                $paymentDetails->payment_type = ($model->due_amount == 0) ? ClientPaymentDetails::PAYMENT_TYPE_FULL : ClientPaymentDetails::PAYMENT_TYPE_PARTIAL;

                if (!$paymentDetails->save(false)) {
                    throw new Exception("PaymentDetails save failed: " . json_encode($paymentDetails->getErrors()));
                }
            } else {
                throw new Exception("PaymentHistory save failed: " . json_encode($paymentHistory->getErrors()));
            }

        } elseif ($type === Sales::TYPE_SALES_UPDATE) {
            // Sales Update with zero payment adjustment
            $paymentHistory = new ClientPaymentHistory([
                'sales_id' => $model->sales_id,
                'client_id' => $model->client_id,
            ]);

            if ($paymentHistory && $paymentHistory->received_amount > 0) {
                $paymentHistory->outletId = $model->outletId;
                $paymentHistory->user_id = $model->user_id;
                $paymentHistory->received_amount = $model->paid_amount;
                $paymentHistory->remaining_amount = 0;
                $paymentHistory->remarks = "This Invoice is modified & paid amount is 0";
                $paymentHistory->status = ClientPaymentHistory::STATUS_APPROVED;
                $paymentHistory->updated_by = $model->user_id;

                if ($paymentHistory->save(false)) {
                    $paymentDetails = self::getOrCreatePaymentDetails($model->client_id, $model->sales_id);
                    $paymentDetails->payment_history_id = $paymentHistory->client_payment_history_id;  // ✅ FIX: use correct PK
                    $paymentDetails->paid_amount = 0;
                    $paymentDetails->payment_type = ($model->due_amount == 0) ? ClientPaymentDetails::PAYMENT_TYPE_FULL : ClientPaymentDetails::PAYMENT_TYPE_PARTIAL;

                    if (!$paymentDetails->save(false)) {
                        throw new Exception("PaymentDetails save failed: " . json_encode($paymentDetails->getErrors()));
                    }
                } else {
                    throw new Exception("PaymentHistory save failed: " . json_encode($paymentHistory->getErrors()));
                }
            }
        }
    }

    private function fillCashBook(CashBook $cashBook, Sales $model): void
    {
        $cashBook->outletId = $model->outletId;
        $cashBook->cash_in = $model->paid_amount;
        $cashBook->cash_out = 0;
        $cashBook->source = CashBook::SOURCE_SALES;
        $cashBook->reference_id = $model->sales_id;
        $cashBook->ref_user_id = Yii::$app->user->id;
        $cashBook->remarks = $model->remarks;
    }

    private function fillDepositBook(DepositBook $depositBook, Sales $model): void
    {
        $depositBook->outletId = $model->outletId;
        $depositBook->bank_id = $model->bank;
        $depositBook->branch_id = $model->branch;
        $depositBook->payment_type_id = $model->payment_type;
        $depositBook->ref_user_id = Yii::$app->user->id;
        $depositBook->deposit_in = $model->paid_amount;
        $depositBook->deposit_out = 0;
        $depositBook->reference_id = $model->sales_id;
        $depositBook->source = DepositBook::SOURCE_SALES;
        $depositBook->remarks = $model->remarks;
    }

    private function recordPaymentEntry(Sales $model, $type, Sales $oldSales): void
    {
        $oldPaid = $oldSales->paid_amount ?? 0;
        $newPaid = $model->paid_amount ?? 0;

        // Skip if both old and new paid amounts are zero → no need to create/update anything
        if ($oldPaid == 0 && $newPaid == 0) {
            return;
        }

        if ($model->paymentTypeModel->type === PaymentType::TYPE_CASH) {
            $cashBook = CashBook::findOne(['reference_id' => $model->sales_id, 'source' => CashBook::SOURCE_SALES]);

            if ($newPaid > 0) {
                if (!$cashBook) {
                    $cashBook = new CashBook(['reference_id' => $model->sales_id, 'source' => CashBook::SOURCE_SALES]);
                }
                $this->fillCashBook($cashBook, $model);
                if (!$cashBook->save(false)) {
                    throw new \Exception("CashBook save failed: " . json_encode($cashBook->getErrors()));
                }

            } elseif ($cashBook && $newPaid == 0 && $oldPaid > 0) {
                // Only clear if there was previously a paid amount
                $cashBook->cash_in = 0;
                $cashBook->cash_out = 0;
                $cashBook->remarks = 'Payment cleared due to invoice update';
                if (!$cashBook->save(false)) {
                    throw new \Exception("CashBook clear failed: " . json_encode($cashBook->getErrors()));
                }
            }

        } elseif ($model->paymentTypeModel->type === PaymentType::TYPE_DEPOSIT) {
            $depositBook = DepositBook::findOne(['reference_id' => $model->sales_id,  'source' => DepositBook::SOURCE_SALES]);

            if ($newPaid > 0) {
                if (!$depositBook) {
                    $depositBook = new DepositBook(['reference_id' => $model->sales_id, 'source' => DepositBook::SOURCE_SALES]);
                }
                $this->fillDepositBook($depositBook, $model);
                if (!$depositBook->save(false)) {
                    throw new \Exception("DepositBook save failed: " . json_encode($depositBook->getErrors()));
                }

            } elseif ($depositBook && $newPaid == 0 && $oldPaid > 0) {
                // Only clear if there was previously a paid amount
                $depositBook->deposit_in = 0;
                $depositBook->deposit_out = 0;
                $depositBook->remarks = 'Payment cleared due to invoice update';
                if (!$depositBook->save(false)) {
                    throw new \Exception("DepositBook clear failed: " . json_encode($depositBook->getErrors()));
                }
            }
        }
    }

    private function updateDetailsAndStatements(Sales $model)
    {
        $detailsUpdated = Yii::$app->db->createCommand()
            ->update(SalesDetails::tableName(), ['status' => SalesDetails::STATUS_APPROVED], ['sales_id' => $model->sales_id])
            ->execute();

        if ($detailsUpdated === false) {
            throw new \Exception("Failed to update sales details.");
        }

        $statementUpdated = Yii::$app->db->createCommand()
            ->update(ProductStatementOutlet::tableName(), ['remarks' => 'Sales - Approved'], ['reference_id' => $model->sales_id])
            ->execute();

        if ($statementUpdated === false) {
            throw new \Exception("Failed to update product statements.");
        }
    }

    private function processClientFinancials(Sales $model, Sales $oldSales)
    {
        $clientId = $model->client_id;

        // 1️⃣ Calculate Sale Amounts (old vs new)
        $newSaleAmount = $model->total_amount - $model->discount_amount;
        $oldSaleAmount = $oldSales->total_amount - $oldSales->discount_amount;

        // 2️⃣ Calculate Paid Amounts (old vs new)
        $newPaid = $model->paid_amount ?? 0;
        $oldPaid = $oldSales->paid_amount ?? 0;

        // 3️⃣ Determine Payment Modes (cash or bank)
        $isCash = $model->paymentTypeModel->type === PaymentType::TYPE_CASH;
        $isBank = $model->paymentTypeModel->type === PaymentType::TYPE_DEPOSIT;

        $newCash = $isCash ? $newPaid : 0;
        $newBank = $isBank ? $newPaid : 0;

        $oldCash = $isCash ? $oldPaid : 0;
        $oldBank = $isBank ? $oldPaid : 0;

        // 4️⃣ If it's an update → call updateSaleWithOptionalPayment
        if ($oldSales->type === Sales::TYPE_SALES_UPDATE) {
            ClientFinancialService::updateSaleWithOptionalPayment(
                $clientId,
                $oldSaleAmount,
                $newSaleAmount,
                $oldCash,
                $newCash,
                $oldBank,
                $newBank,
                ClientFinancialService::REF_TABLE_SALE,
                $model->sales_id,
                'Sales updated after approval',
                Yii::$app->user->id
            );

        } else {
            // 5️⃣ If it's a new sale → call recordSaleWithOptionalPayment
            ClientFinancialService::recordSaleWithOptionalPayment(
                $clientId,
                $newSaleAmount,
                $newCash,
                $newBank,
                ClientFinancialService::REF_TABLE_SALE,
                $model->sales_id,
                'Sales created and approved',
                Yii::$app->user->id
            );
        }
    }

    private function saveSales(Sales $model, $type, Sales $oldSales): array
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {

            // Handle payment adjustment if sales update
            $this->processPaymentDetailsAndHistory($model, $type);

            // Approve sales record
            $model->status = Sales::STATUS_APPROVED;
            $model->type = Sales::TYPE_SALES;

            if ($model->paymentTypeModel->type === PaymentType::TYPE_CASH) {
                $model->bank = null;
                $model->branch = null;
            }

            if (!$model->save()) {
                throw new Exception("Sales save failed: " . json_encode($model->getErrors()));
            }

            // Record payment entry
            $this->recordPaymentEntry($model, $type, $oldSales);

            // Update sales details and product statements
            $this->updateDetailsAndStatements($model);

            // Add Customer Financial Service here:
            $this->processClientFinancials($model, $oldSales);

            $transaction->commit();

            return ['success' => true, 'message' => 'Sales invoice approved successfully.'];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => $e->getMessage(), "details" => $model->getErrors()];
        }
    }

}
