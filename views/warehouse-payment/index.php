<?php

use app\components\SystemSettings;
use app\components\CommonUtility;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\WarehousePayment;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WarehousePaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Warehouse Statement');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'warehouse_daily_statement'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');

$this->registerJsFile(
    '@web/js/approvedModal.js',
    ['depends' => [JqueryAsset::className()]]
);

?>

<?php

    Utility::gridViewModal($this, $searchModel);

    Utility::getMessage();
?>


<div class="warehouse-payment-index">

    <?php

        $gridColumns = [

        [
            'class' => 'kartik\grid\SerialColumn',
            'header'=>'#',
            'hAlign'=>GridView::ALIGN_LEFT,
            //'pageSummary'=>true,
            //'pageSummaryFunc'=>GridView::F_COUNT,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Date',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return DateTimeUtility::getDate($model->created_at, SystemSettings::dateTimeFormat());
            }
        ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'ID',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->id;
                }
            ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'User',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return $model->user->username;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Remarks',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return $model->remarks;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Month',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return CommonUtility::getMonthName($model->month). ', '.$model->year;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Payment Type',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return $model->paymentType->payment_type_name;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'warehouse',
            'hAlign'=>GridView::ALIGN_CENTER,
            'pageSummary' =>"Total",
            'value'=>function($model){
                return $model->warehouse->warehouse_name;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'payment_amount',
            'header'=>'Amount',
            'hAlign'=>GridView::ALIGN_RIGHT,
            'pageSummary' =>true,
            'format'=>['decimal',0],
        ],

        [
            'class'=>'kartik\grid\ActionColumn',
            //'hidden'=>true,
            'vAlign'=>GridView::ALIGN_RIGHT,
            'hiddenFromExport'=>true,
            'hAlign'=>GridView::ALIGN_CENTER,
            'hidden'=>Yii::$app->controller->id=='reports'?true:false,
            'template'=>'{update} {approved}',
            'buttons' => [
                'approved' => function ($url, $model) {
                    if($model->status== WarehousePayment::STATUS_PENDING){
                        return Html::a('<span class="fa fa-check"></span>', Url::to(['view','id'=>Utility::encrypt($model->id)]),[
                            'class'=>'btn btn-default btn-xs approvedButton',
                            'data-pjax'=>0,
                            'title' => Yii::t('app', 'Approve '.$this->title.'# '.$model->payment_amount),
                        ]);
                    }
                },

                'update' => function ($url, $model) {
                    if(DateTimeUtility::getDate($model->created_at, 'd-m-Y')==DateTimeUtility::getDate(null, 'd-m-Y')) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update','id'=>Utility::encrypt($model->id)]),[
                            'class'=>'btn btn-info btn-xs',
                            'data-pjax'=>0,
                            'title' => Yii::t('app', 'Update '.$this->title.'# '.$model->payment_amount),
                        ]);
                    }else{
                        return 'N/A';
                    }
                }
            ],

        ]
    ];

        if(Yii::$app->controller->id=='report'){
            $colspan = 9;
        }else{
            $colspan = 9;
        }

        $button = 'Create';

        yii\widgets\Pjax::begin(['id'=>'warehousePaymentpjaxGridView']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();
    ?>


</div>
