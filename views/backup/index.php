<?php

use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BackupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Backups');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backup-index">


    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Database Backup</h3>
            <div class="box-tools pull-right">
                <?= Html::a(Yii::t('app', 'Add Backup'), ['create'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
        <div class="box-body">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'note',
                    'size',
                    'status',
                    'date',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{dl} {rt} {delete}',
                        'buttons' => [
                            'rt' => function ($url, $model) {
                                return Html::a('<span>Restore</span>', ['backup/restore', 'id'=>$model->id], ['class'=>'btn btn-info btn-xs', 'data-pjax'=>0]);
                            },
                            'dl' => function ($url, $model) {
                                return Html::a('<span>Download</span>', BaseUrl::base(true).'/db/'.$model->name, ['class'=>'btn btn-success btn-xs', 'target'=>'_blank', 'data-pjax'=>0]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('<span>Delete</span>', ['backup/delete', 'id'=>$model->id],
                                    [
                                        'class'=>'btn btn-danger btn-xs',
                                        'target'=>'_blank',
                                        'data-method'=>'post',
                                        'data-pjax'=>1,
                                        'data-confirm'=>'Are you sure you want to delete this item?',
                                        'aria-label'=>'Delete',
                                        'title'=>'Delete'
                                    ]);
                            },
                        ],
                    ],

                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>



</div>
