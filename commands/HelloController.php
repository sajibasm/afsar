<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\components\DateTimeUtility;
use app\components\ProductUtility;
use app\components\Utility;
use app\models\Outlet;
use app\models\ProductStatementOutlet;
use app\models\ProductStock;
use app\models\ProductStockItemsOutlet;
use app\models\ProductStockOutlet;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Json;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }

    private function outletStockMovement($statement, $items)
    {
        $transaction = Yii::$app->db->beginTransaction();

        echo PHP_EOL . "Call outletStockMovement " . count($statement) . PHP_EOL;

        try {

            $productStock = new ProductStock();
            $productStock->setScenario('migration');
            $productStock->type = ProductStock::TYPE_TRANSFER;
            $productStock->invoice_no = Utility::genInvoice('STO-');
            $productStock->status = ProductStock::STATUS_PENDING;
            $productStock->user_id = 1;
            $productStock->status = ProductStock::STATUS_ACTIVE;
            $outlet = Outlet::findOne(self::OUTLET_ID);

            if ($productStock->save()) {

                $productStock->params = Json::encode(['receivedOutlet' => $outlet->name, 'coreStock' => $productStock->product_stock_id]);

                if ($productStock->save()) {

                    $productStockOutlet = new ProductStockOutlet();
                    $productStockOutlet->product_stock_outlet_code = uniqid(rand(1, 9999));
                    $productStockOutlet->invoice = Utility::genInvoice('STR-');
                    $productStockOutlet->ref = $productStock->product_stock_id;
                    $productStockOutlet->note = 'Migration';
                    $productStockOutlet->type = ProductStockOutlet::TYPE_RECEIVED;
                    $productStockOutlet->remarks = $productStock->remarks;
                    $productStockOutlet->params = $productStock->params;
                    $productStockOutlet->transferFrom = ProductStockOutlet::TRANSFER_FROM_STOCK;
                    $productStockOutlet->transferOutlet = -1;
                    $productStockOutlet->receivedFrom = ProductStockOutlet::TRANSFER_FROM_OUTLET;
                    $productStockOutlet->receivedOutlet = self::OUTLET_ID;
                    $productStockOutlet->transferBy = 1;
                    $productStockOutlet->status = ProductStockOutlet::STATUS_ACTIVE;
                    if ($productStockOutlet->save()) {

                        for ($i = 0; $i < count($statement); $i++) {
                            $statement[$i]['reference_id'] = $productStockOutlet->product_stock_outlet_id;
                            $items[$i]['product_stock_outlet_id'] = $productStockOutlet->product_stock_outlet_id;
                        }

                        $totalBulkInsert = Yii::$app->db->createCommand()->batchInsert(ProductStockItemsOutlet::tableName(), [
                            'product_stock_outlet_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price',
                            'retail_price', 'previous_quantity', 'new_quantity', 'total_quantity', 'transferOutlet',
                            'receivedOutlet', 'status',
                        ], $items)->execute();

                        $totalStatementInsert = Yii::$app->db->createCommand()->batchInsert(ProductStatementOutlet::tableName(), [
                            'outlet_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'type',
                            'remarks', 'reference_id', 'user_id', 'created_at', 'updated_at'
                        ], $statement)->execute();


                        if ($totalBulkInsert && $totalStatementInsert) {
                            $transaction->commit();
                        } else {
                            $transaction->rollBack();
                        }
                    } else {
                        print_r($productStockOutlet->getErrors());
                    }
                }
            } else {
                print_r($productStock->getErrors());
            }
        } catch (\Exception $e) {
            print_r($e);
            $transaction->rollBack();
        }

    }

    public function StockToOutlet()
    {
        $statementRows = [];
        $itemsRows = [];

        echo PHP_EOL . "Start StockOutlet " . PHP_EOL;
        $records = Yii::$app->db->createCommand("SELECT * FROM `product_statement` GROUP BY `size_id`")->queryAll();
        echo PHP_EOL . "Total " . count($records) . PHP_EOL;

        for ($i = 0; $i < count($records); $i++) {
            $record = $records[$i];

            $sizeId = (int)$record['size_id'];

            echo PHP_EOL . "Start StockOutlet " . $sizeId . PHP_EOL;

            if ($sizeId > 0) {
                $qty = ProductUtility::getTotalQuantity($sizeId);

                if ($qty>0) {

                    $statementRows[] = [
                        'outlet_id' => self::OUTLET_ID,
                        'item_id' => $record['item_id'],
                        'brand_id' => $record['brand_id'],
                        'size_id' => $record['size_id'],
                        'quantity' => $record['quantity'],
                        'type' => 'Stock-Outlet-Transfer',
                        'remarks' => 'Migration',
                        'reference_id' => '',
                        'user_id' => $record['user_id'],
                        'created_at' => DateTimeUtility::getDate('NOW', 'Y-m-d H:i:s'),
                        'updated_at' => DateTimeUtility::getDate('NOW', 'Y-m-d H:i:s'),
                    ];

                    $itemsRows[] = [
                        'product_stock_outlet_id' => '',
                        'item_id' => $record['item_id'],
                        'brand_id' => $record['brand_id'],
                        'size_id' => $record['size_id'],
                        'cost_price' => 0,
                        'wholesale_price' => 0,
                        'retail_price' => 0,
                        'previous_quantity' => 0,
                        'new_quantity' => 0,
                        'total_quantity' => 0,
                        'transferOutlet' => -1,
                        'receivedOutlet' => self::OUTLET_ID,
                        'status' => 'done',
                    ];
                }


            }


            if (count($statementRows) > 100) {
                $this->outletStockMovement($statementRows, $itemsRows);
                $statementRows = [];
                $itemsRows = [];
            }
        }

        $this->outletStockMovement($statementRows, $itemsRows);

    }

}
