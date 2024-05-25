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
                <th>wholesale price</th>
                <th>retail_price</th>
                <th>new quantity</th>
                <th>total quantity</th>
                <th>status</th>
            </tr>

            <tr>
                <?php foreach ($model as $item): ?>
                    <td><?= $item->item_id ?></td>
                    <td><?= $item->brand_id ?></td>
                    <td><?= $item->size_id ?></td>
                    <td><?= $item->wholesale_price ?></td>
                    <td><?= $item->retail_price ?></td>
                    <td><?= $item->new_quantity ?></td>
                    <td><?= $item->total_quantity ?></td>
                    <td><?= $item->status ?></td>
                <?php endforeach; ?>
            </tr>
        </table>
    </div>

</div>




