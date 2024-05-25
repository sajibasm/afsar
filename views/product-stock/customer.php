<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$titleSub = '';
$params = Yii::$app->request->get('SalesDetailsSearch');
if(!empty($params['created_at'])){
    $titleSub = DateTimeUtility::getDate($params['created_at'], 'd-M-Y').' TO '.DateTimeUtility::getDate($params['created_to'], 'd-M-Y');
}

$this->title = Yii::t('app', 'Total Purchase Of Customer ');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'Potential Customer By Brand'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');

$searchParams = Yii::$app->request->get('SalesDetailsSearch');

?>

<?php
    Utility::gridViewModal($this, $searchModel, '_search_customer');
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
                'header'=>'Customer',
                'attribute' => 'client_name',
                'value'=>function($model){
                    return $model->sales->client_name;
                },
            ],
            [
                'attribute' => 'brand',
                'contentOptions' => ['style' => 'width:100px;'],
                'value'=>function ($model, $key, $index, $widget) {
                    $params = Yii::$app->request->get('SalesDetailsSearch');
                    if(!empty($params['brand_id'])){
                        return $model->brand->brand_name;
                    }
                    return '';
                }
            ],
            [
                'attribute' => 'item',
                'contentOptions' => ['style' => 'width:100px;'],
                'value'=>function ($model, $key, $index, $widget) {
                    $params = Yii::$app->request->get('SalesDetailsSearch');
                    if(!empty($params['item_id'])){
                        return $model->item->item_name;
                    }
                    return '';
                },
            ],

            [
                'attribute' => 'size',
                'contentOptions' => ['style' => 'width:100px;'],
                'pageSummary' => "Total ",
                'value'=>function ($model, $key, $index, $widget) {
                    $params = Yii::$app->request->get('SalesDetailsSearch');
                    if(!empty($params['size_id'])){
                        return $model->size->size_name;
                    }
                    return '';
                }
            ],

            [
                'header'=>'Total',
                'attribute' => 'total_amount',
                'hAlign'=>'right',
                'pageSummary' => true,
                'format'=>['decimal', 0],
                'contentOptions' => ['style' => 'width:100px;'],
                'pageSummaryOptions' => ['prepend' => ''],

                'value'=>function ($model, $key, $index, $widget) {
                    return $model->total_amount;
                },
            ],

        ];

        $title = 'Brand Wise Report';
        if(Yii::$app->controller->id=='report'){
            $colspan = 10;
        }else{
            $colspan = 10;
        }

        $button = 'New Stock';

        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title.$titleSub, $colspan, $exportFileName);

    ?>


</div>
