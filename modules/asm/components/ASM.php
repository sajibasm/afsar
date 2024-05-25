<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 2/01/2020
 * Time: 3:01 AM
 */

namespace app\modules\asm\components;

use app\modules\asm\models\ModulePermission;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\base\Component;

class ASM extends Component
{
    public function assignedPermission($userId)
    {
        return ArrayHelper::map(ModulePermission::find()->where(['userId' => $userId])->orderBy('id')->all(), 'module_action_id', 'id', 'module');
    }

    public function getPermissionList()
    {
        $actions = [];
        $rows = (new \yii\db\Query())
            ->select('modules_action.id aId, modules.id, modules.controller, modules.name, modules.icon, modules_action.name as action,')
            ->from('modules_action')
            ->join('LEFT JOIN', 'modules', 'modules.id=modules_action.module')
            ->andWhere('modules.active=:active', [':active' => 1])
            ->andWhere('modules_action.active=:active', [':active' => 1])
            ->orderBy('modules.priority, modules_action.priority')
            ->all();

        foreach ($rows as $row){
            if(isset($actions[$row['id']])){
                $actions[$row['id']]['action'][$row['aId']] = $row['action'];
            }else{
                $actions[$row['id']] = [
                    'id'=>$row['id'],
                    'name'=>$row['name'],
                    'controller'=>$row['controller'],
                    'action'=>[$row['aId'] =>$row['action']]
                ];
            }
        }

        return $actions;
    }

    public function getPermissions()
    {
        $userId = Yii::$app->user->id;
        $asmModule = Yii::$app->getModule('asm');
        if(isset($asmModule->redis) && !Yii::$app->user->isGuest && Yii::$app->cache->redis->hget('userPermission', $userId)){
            return Json::decode(Yii::$app->cache->redis->hget('userPermission', $userId));
        }else{
            $modules = ModulePermission::find()
                ->select('module_permission.id, modules.controller, modules_action.action')
                ->join('LEFT JOIN', 'modules', 'modules.id=module_permission.module')
                ->join('LEFT JOIN', 'modules_action', 'modules_action.id=module_permission.module_action_id')
                ->andWhere('module_permission.userId = :userId', [':userId' => $userId])->asArray(true)->all();
            $permission = ArrayHelper::map($modules, 'action', 'id', 'controller');
            if(isset($asmModule->redis) && !Yii::$app->user->isGuest) {
                Yii::$app->cache->redis->hset('userPermission', $userId, Json::encode($permission));
            }

            return $permission;
        }
    }

    private function check($controller, $action){
        $permissions = $this->getPermissions();
        $allowedRoute = \Yii::$app->getModule('asm')->allowedRoute;
        if(array_key_exists('*', $allowedRoute)){
            return true;
        }elseif( array_key_exists($controller, $allowedRoute) && in_array('*', $allowedRoute[$controller])){
            return true;
        }elseif (array_key_exists($controller, $allowedRoute) && in_array($action, $allowedRoute[$controller])){
            return true;
        }elseif(array_key_exists($controller, $permissions) && array_key_exists($action, $permissions[$controller])){
            return true;
        }else{
            return false;
        }
    }

    public function can($action)
    {
        return $this->check(Yii::$app->controller->id, $action);
    }

    public function has()
    {
        return $this->check(Yii::$app->controller->id, Yii::$app->controller->action->id);
    }

    public function allowedRoute()
    {
        return \Yii::$app->getModule('asm')->allowedRoute;
    }

}