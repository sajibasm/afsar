<?php

namespace app\services;

use app\components\DateTimeUtility;
use app\models\ProductStatementOutlet;
use app\models\Sales;
use app\models\SalesDetails;
use app\models\SalesDraft;
use Yii;

class InvoiceCreateService
{
    public function finalizeCartForInvoice(Sales $salesModel): bool
    {
        $userId = Yii::$app->user->getId();
        $outletId = $salesModel->outletId;

        $cartItems = $this->getCartItems($userId, $outletId, SalesDraft::TYPE_INSERT, NULL);
        if (empty($cartItems)) {
            return false;
        }

        [$salesDetailsRows, $productStatementRows] = $this->buildRows($salesModel, $cartItems);

        return $this->processFinalization($salesModel, $salesDetailsRows, $productStatementRows, false);
    }

    public function finalizeUpdateCartForInvoice(Sales $salesModel): bool
    {
        $userId = Yii::$app->user->getId();
        $salesId = $salesModel->sales_id;
        $outletId = $salesModel->outletId;

        $cartItems = $this->getCartItems($userId, $outletId, null, $salesId);
        if (empty($cartItems)) {
            return false;
        }

        // Skip TYPE_UPDATE_DELETED
        $filteredItems = array_filter($cartItems, fn($item) => $item->type !== SalesDraft::TYPE_UPDATE_DELETED);

        [$salesDetailsRows, $productStatementRows] = $this->buildRows($salesModel, $filteredItems);

        return $this->processFinalization($salesModel, $salesDetailsRows, $productStatementRows, true);
    }

    private function getCartItems(int $userId, int $outletId, ?string $type = null, ?int $salesId = null): array
    {
        $query = SalesDraft::find()->where([
            'user_id' => $userId,
            'outletId' => $outletId,
        ]);

        if ($type !== null) {
            $query->andWhere(['type' => $type]);
        }

        if ($salesId !== null) {
            $query->andWhere(['sales_id' => $salesId]);
        }

        $query->orderBy(['sales_details_id' => SORT_ASC]);
        return $query->all();
    }

    private function buildRows(Sales $salesModel, array $cartItems): array
    {
        $salesDetailsRows = [];
        $productStatementRows = [];
        $now = DateTimeUtility::getDate(null, 'Y-m-d H:i:s', Yii::$app->params['timeZone']);
        $userId = Yii::$app->user->getId();
        $salesId = $salesModel->sales_id;
        $outletId = $salesModel->outletId;

        foreach ($cartItems as $item) {
            $salesDetailsRows[] = [
                $salesId,
                $item->item_id,
                $item->brand_id,
                $item->size_id,
                $item->cost_amount,
                $item->sales_amount,
                $item->total_amount,
                $item->quantity,
                $item->challan_unit,
                $item->challan_unit,
                $item->challan_quantity,
                $item->outletId,
                SalesDetails::STATUS_PENDING
            ];

            $productStatementRows[] = [
                $outletId,
                $item->item_id,
                $item->brand_id,
                $item->size_id,
                -$item->quantity,
                ProductStatementOutlet::TYPE_SALES,
                'Sales - Pending',
                $salesId,
                $userId,
                $now,
                $now
            ];
        }

        return [$salesDetailsRows, $productStatementRows];
    }

    private function processFinalization(Sales $salesModel, array $salesDetailsRows, array $productStatementRows, bool $isUpdate): bool
    {
        $userId = Yii::$app->user->getId();
        $salesId = $salesModel->sales_id;
        $outletId = $salesModel->outletId;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($isUpdate) {
                SalesDetails::deleteAll(['sales_id' => $salesId, 'outletId' => $outletId]);
                ProductStatementOutlet::deleteAll([
                    'reference_id' => $salesId,
                    'type' => ProductStatementOutlet::TYPE_SALES
                ]);
            }

            $salesAttr = [
                'sales_id', 'item_id', 'brand_id', 'size_id', 'cost_amount', 'sales_amount',
                'total_amount', 'quantity', 'unit', 'challan_unit', 'challan_quantity', 'outletId', 'status'
            ];

            $salesInsert = Yii::$app->db->createCommand()
                ->batchInsert(SalesDetails::tableName(), $salesAttr, $salesDetailsRows)
                ->execute();

            if ($salesInsert !== count($salesDetailsRows)) {
                throw new \Exception("SalesDetails insert failed.");
            }

            $productAttr = [
                'outlet_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks',
                'reference_id', 'user_id', 'created_at', 'updated_at'
            ];

            $productInsert = Yii::$app->db->createCommand()
                ->batchInsert(ProductStatementOutlet::tableName(), $productAttr, $productStatementRows)
                ->execute();

            if ($productInsert !== count($productStatementRows)) {
                throw new \Exception("ProductStatement insert failed.");
            }

            // Delete SalesDraft after success
            $deleteCondition = [
                'user_id' => $userId,
                'outletId' => $outletId
            ];
            if ($isUpdate) {
                $deleteCondition['sales_id'] = $salesId;
            }

            SalesDraft::deleteAll($deleteCondition);

            $transaction->commit();
            return true;

        } catch (\Throwable $e) {
            Yii::error("Invoice finalization failed: " . $e->getMessage(), __METHOD__);
            $transaction->rollBack();
            return false;
        }
    }
}

?>