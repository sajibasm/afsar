<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */

namespace app\components;

use app\models\Outlet;
use app\models\UserOutlet;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class OutletUtility
{

    public static function getOutletWithDefaultWarehouse($exclude)
    {
        $data = ['Stock'];

        $records = Outlet::find()
            ->where(['status' => 1])
            ->andWhere(['NOT', ['outletId' => $exclude]])
            ->orderBy('name')
            ->all();
        foreach ($records as $record) {
            $data[$record->outletId] = $record->name;
        }

        return $data;
    }


    public static function getOutletConditional($exclude = [])
    {
        $records = Outlet::find()
            ->where(['status' => 1])
            ->andWhere(['NOT', ['outletId' => $exclude]])
            ->orderBy('name')
            ->all();

        return ArrayHelper::map($records, 'outletId', 'name');
    }

    public static function getOutlet($status = 1, $orderByColumn = 'priority', $type = '')
    {
        $where = ['status' => $status];
        if ($type) {
            $where['type'] = $type;
        }

        $records = Outlet::find()->where($where)->orderBy($orderByColumn)->all();
        return ArrayHelper::map($records, 'outletId', 'name');
    }

    public static function getUserOutlet()
    {
        $outlets = [];
        $userId = Yii::$app->user->id;
        $userOutlet = UserOutlet::find()->where(['userId' => $userId])->all();

        foreach ($userOutlet as $outlet) {
            $outlets[] = $outlet->outletId;
        }

        $records = Outlet::find()->where(['outletId' => $outlets])->orderBy('priority')->all();
        return ArrayHelper::map($records, 'outletId', 'name');
    }

    public static function numberOfOutletByUser()
    {
        return count(OutletUtility::getUserOutlet());
    }

    public static function defaultOutletByUser()
    {
        $data = OutletUtility::getUserOutlet();
        return array_key_first($data);
    }




}
