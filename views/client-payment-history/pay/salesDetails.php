<?php
use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\Utility;
use yii\helpers\Html;
use yii\grid\GridView;

?>


<?php
    $serial = 1;
    $grandTotal = $grandDue = $grandLess = $grandReceived = 0;
    $invoice = (CustomerUtility::getDueInvoicePrice($model->client_id));

?>


<div class="summary">Showing <b><?=count($invoice)?></b> invoices</div>
<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <a href="">NO</a>
        </th>
        <th>
            <a href=""">Invoice</a>
        </th>
        <th>
            <a href="">Memo</a>
        </th>

        <th>
            <a href="">Last Date</a>
        </th>

        <th>
            <a href="">Total</a>
        </th>

        <th>
            <a href="">Received</a>
        </th>

        <th>
            <a href="">Less</a>
        </th>


        <th>
            <a href="">Due</a>
        </th>

    </tr>
    </thead>

    <tbody>
    <?php foreach ($invoice as $index=>$list):?>

        <?php
            $salesId = $list->sales_id;
            $memoId = $list->memo_id;
            $lastDate = '';
            $received = $list->received;
            $due = $list->due;
            $less = $list->less;
            $total = $list->total;

            $grandTotal += $total;
            $grandDue += $due;
            $grandLess += $less;
            $grandReceived += $received;
        ?>


    <tr data-key='<?=$serial?>'>
        <td><?=$serial++?></td>
        <td><?=$salesId?></td>
        <td><?=$memoId?></td>
        <td></td>
        <td><?= Utility::asDecimal($total)?></td>
        <td><?= Utility::asDecimal($received)?></td>
        <td><?= Utility::asDecimal($less)?></td>
        <td><?= Utility::asDecimal($due)?></td>
    </tr>
    <?php endforeach;?>


    </tbody>

    <tfoot>
    <tr style="font-weight:bold">
        <td colspan="4" style="text-align: right">Total</td>
        <td><?= Utility::asDecimal($grandTotal)?></td>
        <td><?= Utility::asDecimal($grandReceived)?></td>
        <td><?= Utility::asDecimal($grandLess)?></td>
        <td><?= Utility::asDecimal($grandDue)?></td>
    </tr>

    </tfoot>

</table>



