<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;



use app\models\CashBook;
use app\models\CustomerAccount;
use app\models\DepositBook;
use app\models\Sales;
use app\models\SalesDetails;
use app\models\SalesDraft;
use app\models\Serialize;
use Yii;

class TransactionRestore
{

    public static function receivedPayment()
    {

    }
    
    public static function sales($salesId)
    {

        $hasError = false;

        $transaction = Yii::$app->db->beginTransaction();

        try{

            $serialize = Serialize::find()->where(['refId'=>$salesId, 'source'=>Serialize::getTable(Sales::tableName()), 'status'=>Serialize::STATUS_PENDING])->one();
            $sales  =  Sales::findOne($salesId);
            $sales->setAttributes(Serialize::decrypt($serialize->data));
            if($sales->save()){
                if(!$serialize->delete()){
                    $hasError = true;
                }
            } else{
                $hasError = true;
            }

            SalesDetails::deleteAll(['sales_id'=>$salesId]);

            SalesDraft::deleteAll(['sales_id'=>$salesId]);

            $serializes = Serialize::find()->where(['refId'=>$salesId, 'source'=>Serialize::getTable(SalesDetails::tableName()), 'status'=>Serialize::STATUS_PENDING])->all();
            foreach ($serializes as $serialize){
                $salesDetails = new SalesDetails();
                $salesDetails->setAttributes(Serialize::decrypt($serialize->data));
                if($salesDetails->save()){
                    if(!$serialize->delete()){
                        $hasError = true;
                    }
                } else{
                    $hasError = true;
                }
            }

            $serializes = Serialize::find()->where(['refId'=>$salesId, 'source'=>Serialize::getTable(CustomerAccount::tableName()), 'status'=>Serialize::STATUS_PENDING])->all();

            foreach ($serializes as $serialize){
                $customerAccounts = new CustomerAccount();
                $customerAccounts->setAttributes(Serialize::decrypt($serialize->data));
                if($customerAccounts->save()){
                    if(!$serialize->delete()){
                        $hasError = true;
                    }
                } else{
                    $hasError = true;
                }
            }


            $serialize = Serialize::find()->where(['refId'=>$salesId, 'source'=>Serialize::getTable(CashBook::tableName()), 'status'=>Serialize::STATUS_PENDING])->one();
            if($serialize){
                $cashBook = new CashBook();
                $cashBook->setAttributes(Serialize::decrypt($serialize->data));
                if($cashBook->save()){
                    if(!$serialize->delete()){
                        $hasError = true;
                    }
                } else{
                    $hasError = true;
                }
            }else{
                $serialize = Serialize::find()->where(['refId'=>$salesId, 'source'=>Serialize::getTable(DepositBook::tableName()), 'status'=>Serialize::STATUS_PENDING])->one();
                if($serialize){
                    $depositBook = new DepositBook();
                    $depositBook->setAttributes(Serialize::decrypt($serialize->data));
                    if($depositBook->save()) {
                        if(!$serialize->delete()){
                            $hasError = true;
                        }
                    } else{
                        $hasError = true;
                    }
                }
            }

            if($hasError){
                $transaction->rollBack();
            }else{
                $transaction->commit();
            }

        }catch (\Exception $e){
            $transaction->rollBack();
            throw $e;
        }

        return $hasError;
    }

}