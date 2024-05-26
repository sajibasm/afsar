<?php

use app\components\Utility;
use yii\bootstrap\Modal;
    use yii\helpers\Html;
use yii\grid\GridView;
    use yii\helpers\Url;

    /* @var $this yii\web\View */
/* @var $searchModel app\models\BuyerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Supplier');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="buyer-index">


    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Branch</h3>
            <div class="box-tools pull-right">
                <?= Html::a('Add Supplier', ['create'], ['class' => 'btn btn-success', 'data-pjax'=>1])?>
            </div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'name',
                    'city',
                    'address1',
                    'address2',
                    'contact_number',
                    'contact_person',
                    'contact_person_number',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id'=> Utility::encrypt($model->id)],
                                    [
                                        'class'=>'btn btn-info btn-xs',
                                        'data-toggle'=>'tooltip',
                                        'title'=>Yii::t('app', "Update ".$model->name),
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
