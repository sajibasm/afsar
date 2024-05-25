<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockOutlet */
?>
<div class="product-stock-outlet-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'product_stock_outlet_id',
            'product_stock_outlet_code',
            'invoice',
            'note',
            'type',
            'remarks',
            'params:ntext',
            'transferOutlet',
            'receivedOutlet',
            'transferBy',
            'transferApprovedBy',
            'receivedBy',
            'receivedApprovedBy',
            'createdAt',
            'updatedAt',
            'status',
        ],
    ]) ?>

</div>
