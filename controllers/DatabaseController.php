<?php

namespace app\controllers;

use app\components\Utility;
use app\models\Brand;
use app\models\BrandNew;
use app\models\Item;
use app\models\Size;
use Yii;
use app\models\AppSettings;
use app\models\AppSettingsSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppSettingsController implements the CRUD actions for AppSettings model.
 */
class DatabaseController extends Controller
{
    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ]
            ]
        ];
    }

    /**
     * @param \yii\base\Action $event
     * @return bool|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($event){
        if(Yii::$app->asm->has()){
            return parent::beforeAction($event);
        }else{
            return Yii::$app->user->isGuest? $this->redirect(['/site/login']): $this->redirect(['/site/permission']);
        }
    }

    private function uniqueBrand()
    {
        $data = [];
        $sql = "SELECT `id`, `brand`, `item_id` FROM `asm_brand` WHERE `brand`!='' GROUP by `brand` ";
        $rows = Yii::$app->db2->createCommand($sql)->queryAll();

        $model = new BrandNew();

        foreach ($rows as $row){
            $newName = trim($row['brand']);
            if(!empty($newName) && $newName!=null){
                $extra = [];
                $sql2 = "SELECT * FROM `asm_brand` WHERE `brand` LIKE '".$row['brand']."'";
                $newRows = Yii::$app->db2->createCommand($sql2)->queryAll();
                foreach ($newRows as $nRow){
                    if(!empty($nRow['item_id'])) {
                        $extra[]  = (int) trim($nRow['id']);
                    }
                }

                $data[] = [
                    'id'=>null,
                    'name'=>trim($row['brand']),
                    'status'=>BrandNew::STATUS_ACTIVE,
                    'extra'=>json_encode($extra)
                ];
            }
        }

        Yii::$app->db->createCommand()->batchInsert(BrandNew::tableName(), $model->attributes(), $data)->execute();
    }

    private function item()
    {
        $data = [];
        $sql = "SELECT * FROM `asm_item`";
        $rows = Yii::$app->db2->createCommand($sql)->queryAll();

        $model = new Item();
        foreach ($rows as $row){
            if(!empty($row['item']) && $row['item']!=''){
                $data[] = [
                    'item_id'=>$row['id'],
                    'item_name'=>trim($row['item']),
                    'product_status'=>Item::STATUS_ACTIVE,
                ];
            }
        }

        Yii::$app->db->createCommand()->batchInsert(Item::tableName(), $model->attributes(), $data)->execute();
    }

    private function relocatedBrandSize()
    {
        $brand  = [];
        $brandRow = [];
        $sizeRow = [];
        $models = BrandNew::find()->all();
        foreach ($models as $m){
            $array = json_decode($m->extra);
            foreach ($array as $key=>$value){
                $brand[$value] = [
                    'id'=>$m->id,
                    'name'=>$m->name
                ];
            }
        }

        ksort($brand);
        //Utility::debug($brand);

        $sql = "SELECT * FROM `asm_brand`";
        $rows = Yii::$app->db2->createCommand($sql)->queryAll();
        foreach ($rows as $row){
            if(!empty($row['brand']) && $row['brand']!=''){
                $brandRow[] = [
                    'id'=>null,
                    'brand_id'=>(int)$brand[$row['id']]['id'],
                    'item_id'=>(int)$row['item_id'],
                    'brand_name'=>$brand[$row['id']]['name'],
                    'product_status'=>Brand::STATUS_ACTIVE,
                ];
            }
        }

        $brandModel = new Brand();
        //Yii::$app->db->createCommand()->batchInsert(Brand::tableName(), $brandModel->attributes(), $brandRow)->execute();


        $sql = "SELECT * FROM `asm_size`";
        $rows = Yii::$app->db2->createCommand($sql)->queryAll();
        foreach ($rows as $row){
            if(isset($brand[$row['brand_id']]) && !empty($row['size']) && $row['size']!='') {
                $sizeRow[] = [
                    'size_id' => null,
                    'brand_id' => $brand[$row['brand_id']]['id'],
                    'item_id' => $row['item_id'],
                    'size_name' => $row['size'],
                    'size_status' => Size::STATUS_ACTIVE,
                ];
            }
        }


        $sizeModel = new Size();
        Yii::$app->db->createCommand()->batchInsert(Size::tableName(), ['size_id', 'brand_id', 'item_id', 'size_name', 'size_status'], $sizeRow)->execute();


    }

    public function actionProduct()
    {
        //$this->uniqueBrand();
        //$this->item();
        $this->relocatedBrandSize();


    }

}
