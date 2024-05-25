<?php

use app\components\CommonUtility;
use app\models\PaymentType;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;


/* @var $this yii\web\View */
/* @var $model app\models\SalaryHistory */

$this->title = Yii::t('app', 'Remuneration');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payroll'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$var = "bankType='". PaymentType::TYPE_DEPOSIT."'; ";
$var.= 'var type = {';
foreach(CommonUtility::getPaymentType() as $type){
    $var = $var." ".$type->payment_type_id.": '".$type->type."', ";
}
$var = rtrim($var, ', ');
$var=$var.' };';

$this->registerJs($var, View::POS_HEAD, 'paymentType');

$this->registerJs("var salaryCheck='".Url::base(true).'/'.Yii::$app->controller->id.'/check-salary'."';", View::POS_END, 'checkSalary');
$this->registerJsFile(Url::base(true).'/js/employeeAjax.js', ['depends'=> JqueryAsset::className()]);

?>

<div class="box box-info">
<div class="box-header with-border">
    <h3 class="box-title">Remuneration</h3>
    <div class="box-tools pull-right"></div>
</div>
<div class="box-body" id="payroll-Remuneration">

    <div class="salary-history-create">

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>

</div>



