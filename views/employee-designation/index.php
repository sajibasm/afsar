<?php

use app\components\Utility;
use app\models\Employee;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EmployeeDesignationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employee Roles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-designation-index">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Employee Roles</h3>
            <div class="box-tools pull-right">
                <?= Html::a('Add Role', ['create'], ['class' => 'btn btn-info', 'data-pjax'=>1])?>
            </div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'name',
                    [
                        'attribute'=>'status',
                        'value'=>function($model){
                            return $model->status==Employee::ACTIVE_STATUS?Employee::ACTIVE_STATUS_LABEL:Employee::INACTIVE_STATUS_LABEL;
                        }
                    ],
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
