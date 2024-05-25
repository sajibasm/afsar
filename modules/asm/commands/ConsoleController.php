<?php

namespace app\modules\asm\commands;

use app\modules\asm\models\ModulePermission;
use app\modules\asm\models\Modules;
use app\modules\asm\models\ModulesAction;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\helpers\Json;

class ConsoleController extends \yii\console\Controller
{

    private function readAllRoute()
    {
        $fulllist = [];
        $controllerlist = [];

        $appControllerPath =  Yii::getAlias('@app/controllers');

        if(is_dir($appControllerPath)){
            $fileLists =  FileHelper::findFiles($appControllerPath);
            foreach($fileLists as $controllerPath) {
                if(strpos($controllerPath, ".php") !== false){
                    $controllerlist[] = substr($controllerPath,  strrpos($controllerPath, DIRECTORY_SEPARATOR)+1,-4);
                }
            }
        }

        asort($controllerlist);

        foreach ($controllerlist as $controller) {
            $handle = fopen("$appControllerPath/" . $controller . ".php", "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/public function action(.*?)\(/', $line, $display)){
                        if (strlen($display[1]) > 2) {
                            $name = ltrim(strtolower(implode('-', preg_split('/(?=[A-Z])/', str_replace('Controller', '', $controller)))), '-');
                            $fulllist[$name][] = ltrim(strtolower(implode('-', preg_split('/(?=[A-Z])/', $display[1]))), '-');
                        }
                    }
                }
            }
            fclose($handle);
        }


        $controllerlist = [];
        $appControllerPath =  Yii::getAlias('@app/modules/asm/controllers');

        if(is_dir($appControllerPath)){
            $fileLists =  FileHelper::findFiles($appControllerPath);
            foreach($fileLists as $controllerPath) {
                if(strpos($controllerPath, ".php") !== false){
                    $controllerlist[] = substr($controllerPath,  strrpos($controllerPath, DIRECTORY_SEPARATOR)+1,-4);
                }
            }
        }

        asort($controllerlist);

        foreach ($controllerlist as $controller) {
            $handle = fopen("$appControllerPath/" . $controller . ".php", "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/public function action(.*?)\(/', $line, $display)){
                        if (strlen($display[1]) > 2) {
                            $name = ltrim(strtolower(implode('-', preg_split('/(?=[A-Z])/', str_replace('Controller', '', $controller)))), '-');
                            $fulllist[$name][] = ltrim(strtolower(implode('-', preg_split('/(?=[A-Z])/', $display[1]))), '-');
                        }
                    }
                }
            }
            fclose($handle);
        }




        return $fulllist;
    }

    public function actionDefault()
    {

        $data = $this->readAllRoute();



        $defaultRoute = Yii::$app->asm->allowedRoute();

        foreach ($data as $controller => $actions) {

            $module = new Modules();
            $module->name = str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
            $module->name = preg_replace('/(?<!\ )[A-Z]/', ' $0', $module->name);
            $module->code = md5(uniqid());
            $module->controller = $controller;
            $module->priority = count($data);
            $module->icon = 'fa';
            $module->active = 1;
            if ($module->save()) {
                $rows = [];
                $actionList = $actions;
                if (array_key_exists($controller, $defaultRoute)) {
                    $actionList = array_merge(array_diff($actions, $defaultRoute[$controller]), array_diff($actions, $defaultRoute[$controller]));
                }

                foreach ($actionList as $ctr) {
                    $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $ctr)));
                    $name = preg_replace('/(?<!\ )[A-Z]/', ' $0', $name);
                    $rows[] = [$module->id, md5(uniqid() . $module->id), $name, $ctr, 1, count($actionList)];
                }

                if (count($rows) > 0) {
                    Yii::$app->db->createCommand()->batchInsert(ModulesAction::tableName(), ['module', 'code', 'name', 'action', 'active', 'priority'], $rows)->execute();
                }

            }
        }
    }

    private function listOfController()
    {
        return [

            'sales',
            'market-book',
            'sales-return',
            'product-stock',
            'product-stock-outlet',
            'product-stock-movement',
            'product-statement-outlet',
            'withdraw',
            'cash-hand-received',

            'lc-payment',
            'expense',
            'warehouse-payment',
            'bank-reconciliation',

            'reports',
            'client',
            'customer-withdraw',
            'client-payment-history',
            'client-payment-details',
            'customer-account',
            'salary-history',

            'employee-designation',
            'employee',

            'sales-draft',
            'item',
            'brand-map',
            'size',
            'product-items-price',

            'payment-type',
            'expense-type',
            'lc-payment-type',
            'reconciliation-type',
            'challan-condition',

            'lc',
            'product-unit',
            'city',
            'bank',
            'branch',
            'buyer',
            'transport',
            'warehouse',
            'app-settings',
            'outlet',
            'template',
            'email-queue',
            'sms-gateway',
            'backup',

            'cron-job',
        ];
    }

    private function import()
    {
        $this->stdout("Hello");

        $controllers = $this->readAllRoute();
        $defaultRoute = require Yii::getAlias('@modules/asm/Route.php');
        $mdlCounter = 0;
        foreach ($controllers as $controller=>$actions){
            $module = new Modules();
            $module->name = str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
            $module->name = preg_replace('/(?<!\ )[A-Z]/', ' $0', $module->name);
            $module->code = md5(uniqid());
            $module->controller = $controller;
            $module->priority = $mdlCounter++;
            $module->icon = 'fa';
            $module->active = 1;
            if ($module->save()) {
                $rows = [];
                $actionList = $actions;
                if (array_key_exists($controller, $defaultRoute)) {
                    $actionList = array_diff($actions, $defaultRoute[$controller]);
                }

                $actCounter = 0;
                foreach ($actionList as $ctr) {
                    $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $ctr)));
                    $name = preg_replace('/(?<!\ )[A-Z]/', ' $0', $name);
                    $rows[] = [$module->id, md5(uniqid() . $module->id), $name, $ctr, 1, $actCounter++];
                }

                if (count($rows) > 0) {
                    $actionsRows = Yii::$app->db->createCommand()->batchInsert(ModulesAction::tableName(), ['module', 'code', 'name', 'action', 'active', 'priority'], $rows)->execute();
                    if($actionsRows>0){
                        $this->stdout("\nModule #".$controller." Action Has Been Imported\n");
                    }
                }
            }

        }

    }

    public function actionIndex()
    {
        $this->stdout("\nImport Modules and Action \n------------------------------\n");

        $this->stdout("\nRemove Modules");

        Modules::deleteAll();

        $this->stdout("\nRemove Actions");

        ModulesAction::deleteAll();

        $this->stdout("\nRemove Actions Permission");

        ModulePermission::deleteAll();

        $this->import();

        $this->stdout("\nEnd Import");

    }
}