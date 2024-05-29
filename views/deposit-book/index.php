<?php

use app\components\SystemSettings;
use app\components\CommonUtility;
use app\components\DateTimeUtility;
use app\components\Utility;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\DepositBookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Bank Statement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bank Deposit'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'Bank Book Statement '.DateTimeUtility::getDate(null, 'd/M/Y h:s:A');
?>

<?php

    Utility::gridViewModal($this, $searchModel);

    Utility::getMessage();

?>

<div class="deposit-book-index">

    <?php

    $gridColumns = [
        //['class' => 'kartik\grid\SerialColumn'],
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
            'pageSummary' => false,
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model, $key, $value) {
                return $model->id;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Outlet',
            'pageSummary' => false,
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model) {
                return $model->outletDetail->name;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign'=>GridView::ALIGN_LEFT,
            'header'=>'Bank ( Branch )',
            'value'=>function($model){
                return $model->branch->bank->bank_name.'('.$model->branch->branch_name.')';
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign'=>GridView::ALIGN_LEFT,
            'header'=>'Type',
            'value'=>function($model){
                return $model->paymentType->payment_type_name;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'source',
            'pageSummary' => false,
            'hAlign'=>GridView::ALIGN_CENTER,
            //'filterType'=>GridView::FILTER_SELECT2,
            //'group'=>true,
            //'filterWidgetOptions'=>['lc'=>'LC', 'warehouse'=>'WAREHOUSE']
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Remarks(Ref)',
            'attribute' => 'remarks',
            'pageSummary' => 'Total',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model, $key, $value) {
                if(empty($model->remarks)){
                    return "Ref ID:".$model->reference_id;
                }
                return $model->remarks."(Ref ID:".$model->reference_id.")";
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Inflow',
            'attribute' => 'deposit_in',
            'contentOptions' => ['style' => 'width:110px;'],
            'hAlign'=>GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'format'=>['decimal',0],
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Outflow',
            'attribute' => 'deposit_out',
            'contentOptions' => ['style' => 'width:110px;'],
            'hAlign'=>GridView::ALIGN_RIGHT,
            'pageSummary' =>true,
            'format'=>['decimal',0],
        ],

//            [
//                'class' => 'kartik\grid\CheckboxColumn',
//                //'options'=>['class'=>'skip-export']
//            ],

    ];

    if(Yii::$app->controller->id=='report'){
        $colspan = 9;
    }else{
        $colspan = 9;
    }

    $button = '';

    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);

    ?>

</div>
