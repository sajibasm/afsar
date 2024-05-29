<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\ProductStock;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stock Movement/Transfer');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'stock_movement_transfer_statement_'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php
    Utility::getModel();
    Utility::getApprovalModel();
    Utility::getMessage();
?>



<div class="product-stock-index">


    <?php
        $gridColumns = [
            [
                'class' => 'kartik\grid\SerialColumn',
                'header'=>'#'
            ],
            [
                'header'=>'StockId',
                'attribute' => 'product_stock_id',
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->product_stock_id;
                },
                'group'=>false
            ],

            [
                'header'=>'Date',
                'value'=>function($model){
                    return $model->created_at;
                },
                'group'=>false,  //enable grouping
                'subGroupOf'=>0, // StockId column index is the parent group,
            ],

            [
                'header'=>'TrnsId',
                'attribute' => 'invoice_no',
                'hiddenFromExport'=>true,
                'value'=>function($model){
                    return $model->invoice_no;
                },
                'group'=>false,  // enable grouping
            ],

            [
                'header'=>'Type',
                'hiddenFromExport'=>true,
                'attribute' => 'type',
                'value'=>function($model){
                    return $model->type."(".ucfirst($model->status).")";
                },
                'group'=>false,  // enable grouping
                'subGroupOf'=>0 // StockId column index is the parent group,
            ],

            [
                'header'=>'Remarks',
                'hiddenFromExport'=>true,
                'value'=>function($model){
                    return $model->remarks;
                },
                'group'=>false,  // enable grouping
                'subGroupOf'=>0 // StockId column index is the parent group,
            ],

            [
                'header'=>'Extra',
                'hiddenFromExport'=>true,
                'attribute' => 'type',
                'value'=>function($model){
                    if ($model->params) {
                        $json = Json::decode($model->params, false);
                        if($model->type== ProductStock::TYPE_TRANSFER){
                            return SystemSettings::getOutletById($json->outlet)['name'];
                        }elseif($model->type== ProductStock::TYPE_MOVEMENT){
                            return $json->refOutlet->name.' ('.$json->user->name.')';
                        }
                    }
                }
            ],

            [
                'class' => '\kartik\grid\ActionColumn',
                'hiddenFromExport'=>true,
                'header'=>'Action',
                'template'=>' {approved} {details}',
                'buttons' => [

                    'resend' => function ($url, $model) {
                        $json = Json::decode($model->params, false);
                        if ($model->type==ProductStock::TYPE_TRANSFER && $model->status==ProductStock::STATUS_PENDING && $json->send==false) {
                            return Html::a('<span class="fa fa-ban"></span>', Url::to(['sales/view', 'id' => Utility::encrypt($model->product_stock_id)]), [
                                'class' => 'btn btn-success btn-xs approvedButton',
                                'data-pjax' => 0,
                                'title' => Yii::t('app', 'Cancel ' . $this->title . '# ' . $model->product_stock_id),
                            ]);
                        }
                    },

                    'cancel' => function ($url, $model) {
                        if ($model->type==ProductStock::TYPE_TRANSFER && $model->status==ProductStock::STATUS_PENDING) {
                            return Html::a('<span class="fa fa-ban"></span>', Url::to(['sales/view', 'id' => Utility::encrypt($model->product_stock_id)]), [
                                'class' => 'btn btn-default btn-xs approvedButton',
                                'data-pjax' => 0,
                                'title' => Yii::t('app', 'Cancel ' . $this->title . '# ' . $model->product_stock_id),
                            ]);
                        }
                    },


                    'approved' => function ($url, $model) {
                        if ($model->type==ProductStock::TYPE_MOVEMENT && $model->status==ProductStock::STATUS_PENDING) {
                            return Html::a('<span class="fa fa-check"></span>', Url::to(['product-stock/details', 'id' => Utility::encrypt($model->product_stock_id)]), [
                                'class' => 'btn btn-default btn-xs approvedButton',
                                'data-pjax' => 0,
                                'title' => Yii::t('app', 'Approve ' . $this->title . '# ' . $model->product_stock_id),
                            ]);
                        }
                    },

                    'details' => function ($url, $model) {
                        return Html::button('<span class="fa fa-product-hunt"></span>', [
                                'class' => 'btn btn-primary btn-xs modalUpdateBtn',
                                'title' => Yii::t('app', 'Item Details.'),
                                'data-pjax' => 0,
                                'value' => Url::to(['/product-stock-items/details', 'id' => Utility::encrypt($model->product_stock_id)])
                            ]) . '</li>';
                    },
                ],

            ],


        ];

        $title = 'Stock Statement';
        if(Yii::$app->controller->id=='report'){
            $colspan = 16;
        }else{
            $colspan = 17;
        }

        $button = [ Html::a(Yii::t('app', 'Transfer'),['transfer-create'], ['class' => 'btn btn-primary', 'data-pjax'=>0]),];

        yii\widgets\Pjax::begin(['id' => 'stockMovementTransfer']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();
    ?>


</div>
