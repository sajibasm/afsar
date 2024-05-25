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

class TransactionApproved
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
            $serialize->status = Serialize::STATUS_APPVORD;
            $serialize->approved_by = Yii::$app->user->getId();
            if(!$serialize->save()){
                $hasError = true;
            }

            $serializes = Serialize::find()->where(['refId'=>$salesId, 'source'=>Serialize::getTable(SalesDetails::tableName()), 'status'=>Serialize::STATUS_PENDING])->all();
            foreach ($serializes as $serialize){
                $serialize->status = Serialize::STATUS_APPVORD;
                $serialize->approved_by = Yii::$app->user->getId();
                if(!$serialize->save()){
                    $hasError = true;
                }
            }

            $serializes = Serialize::find()->where(['refId'=>$salesId, 'source'=>Serialize::getTable(CustomerAccount::tableName()), 'status'=>Serialize::STATUS_PENDING])->all();
            foreach ($serializes as $serialize){
                $serialize->status = Serialize::STATUS_APPVORD;
                $serialize->approved_by = Yii::$app->user->getId();
                if(!$serialize->save()){
                    $hasError = true;
                }
            }

            $serialize = Serialize::find()->where(['refId'=>$salesId, 'source'=>Serialize::getTable(CashBook::tableName()), 'status'=>Serialize::STATUS_PENDING])->one();
            if($serialize){
                $serialize->status = Serialize::STATUS_APPVORD;
                $serialize->approved_by = Yii::$app->user->getId();
                if(!$serialize->save()){
                    $hasError = true;
                }
            }else{
                $serialize = Serialize::find()->where(['refId'=>$salesId, 'source'=>Serialize::getTable(DepositBook::tableName()), 'status'=>Serialize::STATUS_PENDING])->one();
               if($serialize){
                   $serialize->status = Serialize::STATUS_APPVORD;
                   $serialize->approved_by = Yii::$app->user->getId();
                   if(!$serialize->save()){
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