<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStatementOutlet */
?>
<div class="product-statement-outlet-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'item_id',
                'label' => 'Item',
                'value' => function($model) {
                    return ($model->item_id) ? $model->itemDetail->item_name : '';
                }
            ],
            [
                'attribute'=>'brand_id',
                'label' => 'Brand',
                'value' => function($model) {
                    return ($model->brand_id) ? $model->brandDetail->brand_name : '';
                }
            ],
            [
                'attribute'=>'size_id',
                'label' => 'Size',
                'value' => function($model) {
                    return ($model->size_id) ? $model->sizeDetail->size_name : '';
                }
            ],
            'quantity',
            'type',
            'remarks',
            'reference_id',
            [
                    'attribute' => 'user_id',
                    'label' => 'User',
                    'value' => function($model) {
                        return ($model->user_id) ? $model->userDetail->username : '';
                    }
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
