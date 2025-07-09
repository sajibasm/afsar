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

class StoreUtility
{

    public static function getAvailableStoresWithStock($exclude)
    {
        $outletList = ['Stock'];

        $query = Outlet::find()
            ->where(['status' => 1]);

        if (!empty($exclude)) {
            $query->andWhere(['NOT', ['outletId' => $exclude]]);
        }

        $outlets = $query->orderBy(['name' => SORT_ASC])->all();

        foreach ($outlets as $outlet) {
            $outletList[$outlet->outletId] = $outlet->name;
        }

        return $outletList;
    }


    public static function getAvailableStores(array $exclude = []): array
    {
        $query = Outlet::find()->where(['status' => 1]);

        if (!empty($exclude)) {
            $query->andWhere(['NOT', ['outletId' => $exclude]]);
        }

        $outlets = $query->orderBy(['name' => SORT_ASC])->all();

        return ArrayHelper::map($outlets, 'outletId', 'name');
    }

    public static function getStores($status = 1, $orderByColumn = 'priority', $type = '')
    {
        $conditions = ['status' => $status];

        if (!empty($type)) {
            $conditions['type'] = $type;
        }

        $outlets = Outlet::find()
            ->where($conditions)
            ->orderBy([$orderByColumn => SORT_ASC])
            ->all();

        return ArrayHelper::map($outlets, 'outletId', 'name');
    }

    public static function getUserStores(): array
    {
        $userId = Yii::$app->user->id;
        $userOutlets = UserOutlet::find()
            ->select('outletId')
            ->where(['userId' => $userId])
            ->column();

        if (empty($userOutlets)) {
            return [];
        }

        $outlets = Outlet::find()
            ->where(['outletId' => $userOutlets])
            ->orderBy('priority')
            ->all();

        return ArrayHelper::map($outlets, 'outletId', 'name');
    }


    public static function countUserStores(): int
    {
        return count(self::getUserStores());
    }

    public static function getDefaultStoreByUser(): ?int
    {
        $stores = self::getUserStores();
        return !empty($stores) ? array_key_first($stores) : null;
    }


}
