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
            'item.item_name',
            'brand.brand_name',
            'size.size_name',
            'previous_quantity',
            'new_quantity',
            'total_quantity'
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-success', 'id'=>'approveConfirmation', 'data-view'=>'salesPjaxGridView',  'data-link'=>Url::to(['received-approved','id'=>$id])]) ?>
                <?= Html::button(Yii::t('app', 'Reject'), ['class' => 'btn btn-danger', 'id'=>'cancelConfirmation', 'data-view'=>'salesPjaxGridView',  'data-link'=>Url::to(['received-reject','id'=>$id])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>


</div>
