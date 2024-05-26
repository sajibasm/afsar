<?php

use app\components\Utility;
use app\models\SalesDraft;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesDraftSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Items Stuck');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-draft-index">

    <?php
    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
    ?>

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Items Stuck</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute'=>'sales_id',
                        'header'=>'Invoice',
                        'value'=>function($model){
                            return $model->sales_id;
                        }
                    ],
                    [
                        'attribute'=>'type',
                        'value'=>function($model){
                            return SalesDraft::typeLabel($model->type);
                        }
                    ],
                    [
                        'attribute'=>'item_id',
                        'value'=>'item.item_name'
                    ],
                    [
                        'attribute'=>'brand',
                        'value'=>'brand.brand_name'
                    ],
                    [
                        'attribute'=>'size_id',
                        'value'=>'size.size_name'
                    ],
                    [
                        'attribute'=>'user_id',
                        'value'=>'user.username'
                    ],

                    'quantity',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-remove"></span>', ['delete', 'id'=> Utility::encrypt($model->sales_details_id)],
                                    [
                                        'class'=>'btn btn-default btn-flat',
                                        'data-ajax'=>0,
                                        'data-toggle'=>'tooltip',
                                        'data-method'=>'post',
                                        'title'=>'Update '.$model->item_id,
                                    ]
                                );
                            }
                        ],
                    ],
                ],
            ]); ?>

            <?php yii\widgets\Pjax::end(); ?>

        </div>
    </div>



</div>
