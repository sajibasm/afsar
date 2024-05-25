<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-index">


    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">LC Payment Type</h3>
            <div class="box-tools pull-right">
                <?= Html::a('Add Employee', ['create'], ['class' => 'btn btn-info', 'data-pjax'=>1])?>
            </div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'full_name',
                    'designationModel.name',
                    //'dob',
                    [
                        'header'=>'Picture',
                        'format' => 'html',
                        'value' => function($data) { return Html::img($data->getImageUrl(), ['width'=>'60px', 'height'=>'60px']); },
                    ],
                    'contact_number',
                    //'email:email',
                    'present_address',
                    'permanent_address',
                    'salary',
                    'joining_date',
                    'status',
                    'created_at',
                    // 'updated_at',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span>Update</span>', ['employee/update', 'id'=> Utility::encrypt($model->id)], ['class'=>'btn btn-info btn-xs', 'data-ajax'=>0]);
                            }
                        ],
                    ],
                ],
            ]); ?>
            <?php yii\widgets\Pjax::end(); ?>
        </div>
    </div>

</div>
