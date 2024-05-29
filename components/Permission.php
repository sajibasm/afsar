<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;

class Permission
{
    public static function getPermission()
    {

        $userId = \Yii::$app->user->id;

        $sql = "SELECT m.name as controller, ma.name as action FROM module_permission as mp, modules_action as ma, modules as m WHERE userId=1 GROUP by ANY_VALUE(ma.id) ";

    }
}
