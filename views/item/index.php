<?php

use app\components\Utility;
use yii\bootstrap\Modal;
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\helpers\Url;
    use yii\web\JqueryAsset;
    use yii\web\View;

    /* @var $this yii\web\View */
    /* @var $searchModel app\models\ItemSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */

    $this->title = Yii::t('app', 'Items');
    $this->params['breadcrumbs'][] = $this->title;


?>
<div class="item-index">

    <?php
        Utility::gridViewModal($this, $searchModel);
        Utility::getMessage();
    ?>

    <div class="box box-info">
        <div class="box-header with-border">
            <div class="box-tools pull-left">
                <?= Html::a('Add Item', ['create'], ['class' => 'btn btn-info', 'data-pjax'=>0])?>
            </div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'item_name',
                    'product_status',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id'=> Utility::encrypt($model->item_id)],
                                    [
                                        'class'=>'btn btn-info btn-xs',
                                        'data-ajax'=>0,
                                        'data-toggle'=>'tooltip',
                                        'title'=>'Update '.$model->item_name,
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
