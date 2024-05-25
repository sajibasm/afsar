<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsOutlet */
?>
<div class="product-stock-items-outlet-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'product_stock_items_outlet_id',
            'product_stock_outlet_id',
            'item_id',
            'brand_id',
            'size_id',
            'cost_price',
            'wholesale_price',
            'retail_price',
            'previous_quantity',
            'new_quantity',
            'total_quantity',
            'status',
        ],
    ]) ?>

</div>
