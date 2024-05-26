<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product Stock Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-stock-items-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'item_id',
                'header'=>'Item',
                'value'=>function($data){
                    return $data->item->item_name;
                }
            ],
            [
                'attribute'=>'brand_id',
                'header'=>'Brand',
                'value'=>function($data){
                    return $data->brand->brand_name;
                }
            ],
            [
                'attribute'=>'brand_id',
                'header'=>'Size',
                'value'=>function($data){
                    return $data->size->size_name;
                }
            ],
            [
                'attribute'=>'cost_price',
                'header'=>'Cost',
                'value'=>function($data){
                    return Utility::asDecimal($data->cost_price);
                }
            ],
            [
                'attribute'=>'wholesale_price',
                'header'=>'Wholesale',
                'value'=>function($data){
                    return Utility::asDecimal($data->wholesale_price);
                }
            ],
            [
                'attribute'=>'retail_price',
                'header'=>'Retail',
                'value'=>function($data){
                    return Utility::asDecimal($data->retail_price);
                }
            ],

            [
                'attribute'=>'new_quantity',
                'header'=>'Quantity',
                'value'=>function($data){
                    return Utility::asDecimal($data->new_quantity);
                }
            ]

        ],
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-primary', 'id'=>'approveConfirmation', 'data-view'=>'',  'data-link'=>Url::to(['transfer-approved','id'=>Utility::encrypt($model->product_stock_id)])]) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-warning', 'id'=>'cancelConfirmation', 'data-view'=>'',  'data-link'=>Url::to(['transfer-reject','id'=>Utility::encrypt($model->product_stock_id)])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>

</div>
