<?php

use app\components\Utility;
use yii\bootstrap\Modal;
    use yii\helpers\Html;
use yii\grid\GridView;
    use yii\helpers\Url;

    /* @var $this yii\web\View */
/* @var $searchModel app\models\BranchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Branch');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="branch-index">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Branch</h3>
            <div class="box-tools pull-right">
                <?= Html::a('Add Branch', ['create'], ['class' => 'btn btn-success', 'data-pjax'=>1])?>
            </div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute'=>'bank',
                        'value'=>'bank.bank_name'
                    ],
                    'branch_name',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id'=> Utility::encrypt($model->branch_id)],
                                    [
                                        'class'=>'btn btn-info btn-xs',
                                        'data-toggle'=>'tooltip',
                                        'title'=>Yii::t('app', "Update ".$model->branch_name),
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
