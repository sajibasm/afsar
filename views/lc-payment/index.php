<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\LcPayment;
use app\models\PaymentType;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LcPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'LC Payments');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'lc_daily_statement_'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');

?>

<?php

    Utility::gridViewModal($this, $searchModel);

    Utility::getMessage();
?>




<div class="lc-payment-index">


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
                     return $model->lc_payment_id;
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
                 'header' => 'LC',
                 'hAlign'=>GridView::ALIGN_CENTER,
                 'value'=>function($model){
                     return $model->lc->lc_name;
                 }
             ],


             [
                 'class' => '\kartik\grid\DataColumn',
                 'header' => 'Head',
                 'hAlign'=>GridView::ALIGN_CENTER,
                 'value'=>function($model){
                     return $model->lcPaymentType->lc_payment_type_name;
                 }
             ],

             [
                 'class' => '\kartik\grid\DataColumn',
                 'header' => 'Type',
                 'hAlign'=>GridView::ALIGN_CENTER,
                 'pageSummary' =>"Total",
                 'value'=>function($model){
                     return $model->paymentType->payment_type_name;
                 }
             ],


//             [
//                 'class' => '\kartik\grid\DataColumn',
//                 'header' => 'Bank/Branch',
//                 'hAlign'=>GridView::ALIGN_CENTER,
//                 'value'=>function($model){
//                     if($model->paymentType->payment_type_name== PaymentType::TYPE_DEPOSIT){
//                         return
//                     }
//                 }
//             ],


             [
                 'class' => '\kartik\grid\DataColumn',
                 'attribute' => 'amount',
                 'header'=>'Amount',
                 'hAlign'=>GridView::ALIGN_RIGHT,
                 'pageSummary' =>true,
                 'format'=>['decimal',0],
             ],


             [
                 'class'=>'kartik\grid\ActionColumn',
                 'hidden'=>Yii::$app->controller->id=='reports'?true:false,
                 'vAlign'=>GridView::ALIGN_RIGHT,
                 'hiddenFromExport'=>true,
                 'hAlign'=>GridView::ALIGN_CENTER,
                 'template'=>'{update} {approved}',
                 'buttons' => [

                     'approved' => function ($url, $model) {
                         if($model->status== LcPayment::STATUS_PENDING){
                             return Html::a('<span class="fa fa-check"></span>', Url::to(['view','id'=>Utility::encrypt($model->lc_payment_id)]),[
                                 'class'=>'btn btn-default btn-xs approvedButton',
                                 'data-pjax'=>0,
                                 'title' => Yii::t('app', 'Approve '.$this->title.'# '.$model->amount),
                             ]);
                         }
                     },

                     'update' => function ($url, $model) {
                         if(DateTimeUtility::getDate($model->created_at, 'd-m-Y')==DateTimeUtility::getDate(null, 'd-m-Y')) {
                             return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['update','id'=>Utility::encrypt($model->lc_payment_id)]),[
                                 'class'=>'btn btn-primary btn-xs',
                                 'data-pjax'=>0,
                                 'title' => Yii::t('app', 'Update LC Payment# '.$model->lc_payment_id),
                             ]);
                         }else{
                             return '';
                         }
                     }
                 ],

             ],

         ];

         if(Yii::$app->controller->id=='report'){
             $colspan = 9;
         }else{
             $colspan = 9;
         }

         $button = 'New Payment';


        yii\widgets\Pjax::begin(['id'=>'LCPaymentpjaxGridView']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();



     ?>


</div>
