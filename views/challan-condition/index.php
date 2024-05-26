<?php

use app\components\Utility;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChallanConditionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = Yii::t('app', 'Challan Conditions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="challan-condition-index">

    <p>
        <?= Html::a('Add Challan Condition', ['create'], ['class' => 'btn btn-info', 'data-pjax' => 1]) ?>
    </p>

    <?php yii\widgets\Pjax::begin(['id' => 'pjaxGridView']) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'challan_condition_name',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id' => Utility::encrypt($model->challan_condition_id)],
                            [
                                'class' => 'btn btn-info btn-xs',
                                'data-toggle' => 'tooltip',
                                'title' => Yii::t('app', "Details " . $model->challan_condition_name),
                                'data-ajax' => 0
                            ]
                        );
                    }
                ],
            ],
        ],
        'striped' => true,
        'condensed' => true,
        'responsive' => true,
        'panel' => [
            'type' => 'info',
            'heading' => '<i class="glyphicon glyphicon-list"></i> Sms Gateways listing',
            'before' => '<em>* Resize table columns just like a spreadsheet by dragging the column edges.</em>',
        ]
    ]); ?>

    <?php yii\widgets\Pjax::end(); ?>

</div>
