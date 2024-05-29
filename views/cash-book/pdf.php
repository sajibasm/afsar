<?php

use app\components\SystemSettings;
use app\components\CashUtility;
use app\components\DateTimeUtility;
use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Cash Summery');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Books'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $model app\models\CashBook */
/* @var $form yii\widgets\ActiveForm */
$dateTime =  new DateTime();
$date =  $dateTime->setTimestamp(strtotime($data['date']))->setTimezone(new DateTimeZone(SystemSettings::getTimeZone()))->format(SystemSettings::getDateFormat());

?>

<div class="cash-book-form">


    <div id="w1-container" class="table-responsive kv-grid-container" style="overflow: auto" data-resizable-columns-id="kv-3-fasf-w1">
        <table class="kv-grid-table table table-hover table-bordered table-striped table-condensed kv-table-wrap"><thead>
            <tr>	<th style="width: 100%;" class="text-center warning" colspan="3">CASH SUMMERY (<?=$outlet->name?>)</th></tr>

            <tr class="kartik-sheet-style">
                <th style="width: 18.82%;" class="kv-align-center" data-resizable-column-id="kv-col-1" data-col-seq="1">SOURCE(HEAD)</th>
                <th style="width: 18.82%;" class="kv-align-center" data-resizable-column-id="kv-col-1" data-col-seq="1">DATE</th>
                <th style="width: 18.82%;" class="kv-align-right" data-resizable-column-id="kv-col-1" data-col-seq="1">AMOUNT</th>
            </thead>
            <tbody>

            <tr>
                <td class="kv-align-right kv-align-middle" data-col-seq="1">Opening Balance</td>
                <td class="kv-align-right kv-align-middle" data-col-seq="2"><?= $date?></td>
                <td class="kv-align-right" data-col-seq="3"><?= $data['openingBalance']?></td>
            </tr>

            <tr>
                <td class="kv-align-right kv-align-middle" data-col-seq="4">Sales Collection</td>
                <td class="kv-align-right kv-align-middle" data-col-seq="5"><?= $date?></td>
                <td class="kv-align-right" data-col-seq="6"><?=$data['salesCollection'];?></td>
            </tr>

            <tr>
                <td class="kv-align-right kv-align-middle" data-col-seq="7">Due Received</td>
                <td class="kv-align-right kv-align-middle" data-col-seq="8"><?= $date?></td>
                <td class="kv-align-right" data-col-seq="9"><?= $data['dueReceived'];?></td>
            </tr>


            <tr>
                <td class="kv-align-right kv-align-middle" data-col-seq="7">Advanced Received</td>
                <td class="kv-align-right kv-align-middle" data-col-seq="8"><?= $date?></td>
                <td class="kv-align-right" data-col-seq="9"><?= $data['advancedReceived'];?></td>
            </tr>

            <tr>
                <td class="kv-align-right kv-align-middle" data-col-seq="11">Cash Hand Received</td>
                <td class="kv-align-right kv-align-middle" data-col-seq="12"><?= $date?></td>
                <td class="kv-align-right" data-col-seq="13"><?= $data['cashHandReceived'];?></td>
            </tr>

            <tr>
                <td class="kv-align-right kv-align-middle" colspan="2" align="right" data-col-seq="11"><strong>Total Inflow</strong></td>
                <td class="kv-align-right" data-col-seq="14"><strong><?= $data['totalCashIn']?></strong></td>
            </tr>

            <tr>
                <td class="kv-align-right kv-align-middle" data-col-seq="15">Sales Return</td>
                <td class="kv-align-right kv-align-middle" data-col-seq="16"><?= $date?></td>
                <td class="kv-align-right" data-col-seq="17"><?= $data['salesReturn'];?></td>
            </tr>

            <tr>
                <td class="kv-align-right kv-align-middle" data-col-seq="18">Expense</td>
                <td class="kv-align-right kv-align-middle" data-col-seq="19"><?= $date?></td>
                <td class="kv-align-right" data-col-seq="20"><?= $data['expense'];?></td>
            </tr>

            <tr>
                <td class="kv-align-right kv-align-middle" data-col-seq="18">Withdraw</td>
                <td class="kv-align-right kv-align-middle" data-col-seq="19"><?= $date?></td>
                <td class="kv-align-right" data-col-seq="20"><?= $data['withdraw'];?></td>
            </tr>

            <tr>
                <td class="kv-align-right kv-align-middle" colspan="2" align="right" data-col-seq="11"><strong>Total Outflow</strong></td>
                <td class="kv-align-right" data-col-seq="14"><strong><?= $data['totalCashOut']?></strong></td>
            </tr>

            </tbody>
            <tfoot>
                <tr class="kv-page-summary warning">
                    <td class="kv-align-center" colspan="2" align="right"><strong>Balance</strong></td>
                    <td class="kv-align-right kv-align-middle"><strong><?=$data['balance'];?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>



</div>
