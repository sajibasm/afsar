<?php

use app\components\CommonUtility;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

?>

<div class="product-stock-items-draft-index">
    <div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th>item</th>
                <th>brand</th>
                <th>size</th>
                <th>new quantity</th>
                <th>total quantity</th>
            </tr>

            <tr>
                <?php foreach ($model as $item): ?>
                    <td><?= $item->item_id ?></td>
                    <td><?= $item->brand_id ?></td>
                    <td><?= $item->size_id ?></td>
                    <td><?= $item->new_quantity ?></td>
                    <td><?= $item->total_quantity ?></td>
                <?php endforeach; ?>
            </tr>
        </table>
    </div>

</div>




