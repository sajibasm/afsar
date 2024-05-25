<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LcPaymentTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'LC Payment Type');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lc-payment-type-index">


    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">LC Payment Type</h3>
            <div class="box-tools pull-right">
                <?= Html::a('Add LC Payment Type', ['create'], ['class' => 'btn btn-info', 'data-pjax'=>1])?>
            </div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'lc_payment_type_name',
                    'lc_payment_type_status',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id'=> Utility::encrypt($model->lc_payment_type_id)],
                                    [
                                        'class'=>'btn btn-info btn-xs',
                                        'data-toggle'=>'tooltip',
                                        'title'=>Yii::t('app', "Details ".$model->lc_payment_type_name),
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
