<?php

use app\components\Utility;
use yii\bootstrap\Modal;
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\helpers\Url;
    use yii\web\JqueryAsset;
    use yii\web\View;

    /* @var $this yii\web\View */
    /* @var $searchModel app\models\BrandSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */

    $this->title = Yii::t('app', 'Brand');
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-index">

    <?php
    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
    ?>

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Bands</h3>
            <div class="box-tools pull-right">
                <?= Html::a('Add Brand', ['create'], ['class' => 'btn btn-info', 'data-pjax'=>0])?>
            </div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute'=>'item',
                        'value'=>'item.item_name'

                    ],
                    'brand_name',
                    'brand_status',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id'=> Utility::encrypt($model->brand_id)],
                                    [
                                        'class'=>'btn btn-info btn-xs',
                                        'data-ajax'=>0,
                                        'data-toggle'=>'tooltip',
                                        'title'=>'Update '.$model->brand_name,
                                    ]);
                            }
                        ],
                    ],
                ],
            ]); ?>
            <?php yii\widgets\Pjax::end(); ?>
        </div>
    </div>



</div>
