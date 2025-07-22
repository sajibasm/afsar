<?php


namespace app\services;

use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\ProductStoreUtility;
use app\models\ProductStatement;
use app\models\ProductStatementOutlet;
use app\models\Sales;
use app\models\SalesDetails;
use app\models\SalesDraft;
use app\models\Size;
use mdm\admin\models\User;
use Yii;
use yii\widgets\ActiveForm;

class CartService
{

    /**
     * Update Cart Item: Add, Remove, or Set Quantity
     *
     * @param array $postData
     * @return array
     */
    public function updateCartItem($data): array
    {
        $salesDraftId = $data['editableKey'];
        $editableIndex = $data['editableIndex'];
        $editableAttribute = $data['editableAttribute'];
        $value = $data['SalesDraft'][$editableIndex][$editableAttribute] ?? null;

        $existingDraft = SalesDraft::findOne(['sales_details_id' => $salesDraftId]);

        if (!$existingDraft) {
            return ['output' => '', 'message' => 'Draft item not found.'];
        }

        if ($existingDraft->type === \app\models\SalesDraft::TYPE_UPDATE_DELETED){
            return ['output' => '', 'message' => 'This draft item has already been deleted.'];

        }



        // Convert to correct type for comparison
        if ($editableAttribute === 'quantity') {
            $value = (int)$value;
        } elseif ($editableAttribute === 'sales_amount') {
            $value = (float)$value;
        }

        // Check if value is different before updating
        if ($existingDraft->{$editableAttribute} == $value) {
            return ['output' => '', 'message' => 'No change detected.'];
        }

        // Load stock and price info once if needed
        $productStoreInfo = ProductStoreUtility::getAvailableProductInfo($existingDraft->size_id, $existingDraft->outletId);

        $existingDraft->type = ($existingDraft->type === SalesDraft::TYPE_UPDATE) ? SalesDraft::TYPE_UPDATE_MODIFIED : $existingDraft->type;


        if ($editableAttribute === 'quantity') {
            return $this->updateQuantity($existingDraft, $value, $productStoreInfo['quantity'] ?? 0);
        }

        if ($editableAttribute === 'sales_amount') {
            return $this->updateSalesAmount($existingDraft, $value, $productStoreInfo);
        }

        return ['output' => '', 'message' => 'No editable attribute matched.'];
    }

    private function updateQuantity(SalesDraft $draft, int $quantity, int $availableQty): array
    {
        if ($quantity === 0) {
            $draft->delete();
            return ['output' => '', 'message' => 'Item removed from cart.'];
        }

        if ($quantity > $availableQty) {
            return ['output' => '', 'message' => "Only $availableQty item(s) available in stock."];
        }

        $draft->quantity = $quantity;
        $draft->total_amount = $quantity * $draft->sales_amount;

        if (!$draft->save()) {
            return $this->modelError($draft);
        }

        return ['output' => $quantity, 'message' => ''];
    }

    private function updateSalesAmount(SalesDraft $draft, float $unitPrice, array $info): array
    {
        if ($unitPrice <= 0) {
            return ['output' => '', 'message' => 'Unit price must be greater than 0.'];
        }

        if (empty($info)) {
            return ['output' => '', 'message' => 'Product price information is unavailable.'];
        }

        $minPrice = (float)($info['lowestPrice'] ?? 0);
        $maxPrice = (float)($info['highestPrice'] ?? 0);

        if ($unitPrice < $minPrice || $unitPrice > $maxPrice) {
            return [
                'output' => '',
                'message' => "Invalid unit price. Please enter a value between " .
                    number_format($minPrice, 2) . " and " . number_format($maxPrice, 2) . "."
            ];
        }

        $draft->sales_amount = $unitPrice;
        $draft->total_amount = $unitPrice * $draft->quantity;

        if (!$draft->save()) {
            return $this->modelError($draft);
        }

        return ['output' => number_format($unitPrice, 2), 'message' => ''];
    }

    private function modelError(SalesDraft $model): array
    {
        $errors = array_map(fn($e) => implode(", ", $e), $model->getErrors());
        return ['output' => '', 'message' => implode(", ", $errors)];
    }

    private function quantityError($available, $requested): array
    {
        return [
            'success' => false,
            'message' => "Available Quantity: {$available}, Requested Quantity: {$requested}",
            'type' => 'others',
        ];
    }

    public function addCartItem(array $postData): array
    {
        if (empty($postData['SalesDraft'])) {
            return Yii::$app->json->error('Invalid request data.');
        }

        $data = $postData['SalesDraft'];

        $sizeId = (int)($data['size_id'] ?? 0);
        $storeId = (int)($data['outletId'] ?? 0);
        $userId = (int)($data['user_id'] ?? 0);
        $salesId = (int)($data['sales_id'] ?? null);
        $type = !$salesId? SalesDraft::TYPE_INSERT : SalesDraft::TYPE_UPDATE_ADDED;

        if ($sizeId === 0 || $storeId === 0 || $userId === 0) {
            return Yii::$app->json->error('Missing required product, store or user.');
        }

        $availableQtyInfo = ProductStoreUtility::getAvailableProductInfo($sizeId, $storeId);
        $availableQty = $availableQtyInfo['quantity'];
        $existingDraft = SalesDraft::findOne([
            'size_id' => $sizeId,
            'user_id' => Yii::$app->user->id,
            'outletId' => $storeId,
        ]);

        if ($existingDraft && $existingDraft->type!==SalesDraft::TYPE_UPDATE_DELETED) {
            return Yii::$app->json->error('This product is already in your cart. You can either remove it and add again, or modify it directly from the cart.');
        }

        $draft = new SalesDraft();
        $draft->load(['SalesDraft' => $data]);
        $draft->user_id = $userId;
        $draft->outletId = $storeId;
        $draft->sales_id = $salesId;
        $draft->type = $type;
        $draft->sales_amount = $draft->price;
        $draft->total_amount = $draft->quantity * $draft->sales_amount;

        $sizeModel = Size::findOne($draft->size_id);
        if ($sizeModel && $sizeModel->productUnit) {
            $draft->challan_unit = $sizeModel->productUnit->name;
            $draft->challan_quantity = $sizeModel->unit_quantity;
        }

        if ($draft->quantity > $availableQty) {
            return $this->quantityError($availableQty, $draft->quantity);
        }

        if (!$draft->save()) {
            return Yii::$app->json->error(ActiveForm::validate($draft));
        }

        return Yii::$app->json->success('Cart item added successfully.');
    }

    public function moveCartToProductStatement(Sales $model)
    {
        $salesDetailsRows = [];
        $productStatementRows = [];

        $salesAttr = ['sales_id', 'item_id', 'brand_id', 'size_id', 'cost_amount', 'sales_amount',
            'total_amount', 'quantity', 'unit', 'challan_unit', 'challan_quantity', 'outletId', 'status'];

        $productStatementOutletAttr = ['outlet_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks',
            'reference_id', 'user_id', 'created_at', 'updated_at'
        ];

        $models = SalesDraft::find()->where([
            'user_id' => Yii::$app->user->getId(),
            'type' => SalesDraft::TYPE_INSERT,
            'outletId' => $model->outletId,
        ])->all();

        foreach ($models as $product) {
            $salesDetailsRows[] = [
                $model->sales_id,
                $product->item_id,
                $product->brand_id,
                $product->size_id,
                $product->cost_amount,
                $product->sales_amount,
                $product->total_amount,
                $product->quantity,
                $product->challan_unit,
                $product->challan_unit,
                $product->challan_quantity,
                $product->outletId,
                SalesDetails::STATUS_PENDING
            ];

            $productStatementRows[] = [
                $model->outletId,
                $product->item_id,
                $product->brand_id,
                $product->size_id,
                -$product->quantity,
                ProductStatement::TYPE_SALES,
                'Sales - Pending',
                $model->sales_id,
                Yii::$app->user->getId(),
                DateTimeUtility::getDate(null, 'Y-m-d H:i:s', Yii::$app->params['timeZone']),
                DateTimeUtility::getDate(null, 'Y-m-d H:i:s', Yii::$app->params['timeZone'])
            ];
        }

        $totalSalesDetailsRows = count($salesDetailsRows);
        $totalSalesDetailsInsert = Yii::$app->db->createCommand()
            ->batchInsert(SalesDetails::tableName(), $salesAttr, $salesDetailsRows)
            ->execute();

        if ($totalSalesDetailsInsert == $totalSalesDetailsRows) {
            $productStatementInserted = Yii::$app->db->createCommand()
                ->batchInsert(ProductStatementOutlet::tableName(), $productStatementOutletAttr, $productStatementRows)
                ->execute();

            if ($productStatementInserted == count($productStatementRows)) {
                // ✅ Delete drafts after success
                SalesDraft::deleteAll([
                    'type' => SalesDraft::TYPE_INSERT,
                    'user_id' => $model->user_id,
                    'outletId' => $model->outletId
                ]);
                return true;
            }
        }

        return false;
    }

    public function moveProductsToDraft($salesId): bool
    {
        $userId = Yii::$app->user->id;

        // ✅ Check if a draft exists for this salesId AND the current user
        $existingDraft = SalesDraft::find()
            ->where(['sales_id' => $salesId])
            ->one();

        if ($existingDraft) {
            if($existingDraft->user_id == $userId){
                return true;
            }else{
                $user = User::find()->where(['user_id' => $userId])->one();
                $userName = $user ? ($user->name ?? $user->username ?? $user->email) : 'Unknown user';
                FlashMessage::setMessage(
                    "Invoice is already being edited by $userName",
                    "Sales Update",
                    "error"
                );

                return false;
            }
        }


        $salesDetails = SalesDetails::find()
            ->where(['sales_id' => $salesId])
            ->all();

        if (empty($salesDetails)) {
            FlashMessage::setMessage(
                "No product found for this invoice.",
                "Sales Update",
                "error"
            );
            return false;
        }

        $rows = [];
        foreach ($salesDetails as $item) {
            $rows[] = [
                'sales_id'         => $item->sales_id,
                'outletId'         => $item->outletId,
                'item_id'          => $item->item_id,
                'brand_id'         => $item->brand_id,
                'size_id'          => $item->size_id,
                'cost_amount'      => $item->cost_amount,
                'sales_amount'     => $item->sales_amount,
                'total_amount'     => $item->total_amount,
                'quantity'         => $item->quantity,
                'challan_unit'     => $item->challan_unit,
                'challan_quantity' => $item->challan_quantity,
                'type'             => SalesDraft::TYPE_UPDATE,
                'user_id'          => $userId,
            ];
        }

        $inserted = Yii::$app->db->createCommand()->batchInsert(
            SalesDraft::tableName(),
            [
                'sales_id', 'outletId', 'item_id', 'brand_id', 'size_id', 'cost_amount',
                'sales_amount', 'total_amount', 'quantity', 'challan_unit',
                'challan_quantity', 'type', 'user_id'
            ],
            $rows
        )->execute();

        if ($inserted) {
            FlashMessage::setMessage(
                "Products have been moved to draft stage.",
                "Sales Update",
                "success"
            );
            return true;
        }

        FlashMessage::setMessage(
            "Failed to move products to draft.",
            "Sales Update",
            "error"
        );
        return false;
    }


    /**
     * Check if all SalesDraft items for the given sales_id, user, and outlet are of type TYPE_UPDATE (unchanged).
     *
     * @param int $salesId
     * @param int $userId
     * @param int $outletId
     * @return bool
     */
    public function isCartUnchanged(int $salesId, int $userId, int $outletId): bool
    {
        return !SalesDraft::find()
            ->where([
                'sales_id' => $salesId,
                'user_id' => $userId,
                'outletId' => $outletId,
            ])
            ->andWhere(['not', ['type' => SalesDraft::TYPE_UPDATE]])
            ->exists();
    }

}
