<?php

use app\components\ButtonHelper;
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

<?php Utility::gridViewModal($this, $searchModel); ?>

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
                 'template' => \mdm\admin\components\Helper::filterActionColumn('{update} {approved}'),
                 'buttons' => [

                     'approved' => function ($url, $model) {
                         if ($model->status == LcPayment::STATUS_PENDING) {
                             return ButtonHelper::actionButton('approve', Url::to(['view', 'id' => Utility::encrypt($model->lc_payment_id)]), [
                                 'class' => 'approvedButton',
                                 'title' => Yii::t('app', 'Approve ' . Yii::$app->controller->title . '# ' . $model->amount),
                                 'confirm' => true,  // ✅ Enable SweetAlert confirmation
                                 'confirmTitle' => 'Are you sure?',
                                 'confirmText' => 'This action will approve the LC payment.',
                                 'confirmButton' => 'Yes, approve!',
                                 'cancelButton' => 'Cancel',
                             ]);
                         }
                     },

                     'update' => function ($url, $model) {
                         $isToday = DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y');

                         if ($isToday) {
                             return ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->lc_payment_id)]), [
                                 'icon' => '<span class="glyphicon glyphicon-pencil"></span>',  // ✅ Custom icon if needed
                                 'title' => Yii::t('app', 'Update LC Payment# ' . $model->lc_payment_id),
                             ]);
                         }
                         return '';
                     },

                 ],

             ],

         ];

         if(Yii::$app->controller->id=='report'){
             $colspan = 9;
         }else{
             $colspan = 9;
         }


        yii\widgets\Pjax::begin(['id'=>'LCPaymentpjaxGridView']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();



     ?>


</div>
