<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStatementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product Statements');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-statement-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'header'=>'ID#',
                'value'=>function($model){
                    return $model->product_statement_id;
                }
            ],

            'item.item_name',
            'brand.brand_name',
            'size.size_name',
            'quantity',
            'type',
            'remarks',
            'reference_id',
            'user_id',
            'created_at',
            'updated_at',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
