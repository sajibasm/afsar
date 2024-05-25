<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;

use app\models\ReturnDraft;
use app\models\SalesDraft;
use app\models\User;
use app\models\UserRole;
use yii\helpers\ArrayHelper;

class UserUtility {

    public static function getUserList($status = User::STATUS_ACTIVE, $order='username', $asArray = false)
    {
        $records = User::find()->where(['user_status'=>$status])->orderBy($order)->all();
        if($asArray){
            ArrayHelper::map($records, 'user_id', 'username');
        }else{
            return $records;
        }
    }


    public static function getUserRoleList($status = UserRole::STATUS_ACTIVE, $order = 'user_role_name', $asArray = false)
    {
        $records = UserRole::find()->where("user_role_status='".$status."' AND user_role_name!='Super Admin'")->orderBy($order)->all();
        if($asArray){
            ArrayHelper::map($records, 'user_role_id', 'user_role_name');
        }else{
            return $records;
        }

    }

    public static function getUserStatus()
    {
        return [
            User::STATUS_ACTIVE=>ucwords(User::STATUS_ACTIVE),
            User::STATUS_INACTIVE=>ucwords(User::STATUS_INACTIVE)
        ];
    }


    public static function getRoleByRolePK($id)
    {
        return UserRole::findOne($id);
    }

    public static function removeCartItemsByUser()
    {
        $userId = \Yii::$app->user->getId();
        SalesDraft::deleteAll("user_id=".$userId);
        ReturnDraft::deleteAll("user_id=".$userId);
    }


}