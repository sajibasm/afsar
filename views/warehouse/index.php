<?php

use app\components\DateTimeUtility;
use app\components\Utility;
use yii\bootstrap\Modal;
    use yii\helpers\Html;
use yii\grid\GridView;
    use yii\helpers\Url;

    /* @var $this yii\web\View */
/* @var $searchModel app\models\WarehouseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Warehouse');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-index">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Branch</h3>
            <div class="box-tools pull-right">
                <?= Html::a('Add Warehouse', ['create'], ['class' => 'btn btn-success', 'data-pjax'=>1])?>
            </div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'warehouse_name',
                    'cities.city_name',
                    'address1',
                    'address2',
                    'postal_code',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {

                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id'=> Utility::encrypt($model->warehouse_id)],
                                    [
                                        'class'=>'btn btn-info btn-xs',
                                        'data-toggle'=>'tooltip',
                                        'title'=>Yii::t('app', "Update ".$model->warehouse_name),
                                        'data-ajax'=>0
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
