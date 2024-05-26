<?php

use app\models\AppSettings;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\AppSettingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'App Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-settings-index">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">App Settings</h3>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    'id',
                    [
                        'attribute'=>'app_options',
                        'value'=>function($model){
                            return ucwords(strtolower(str_replace('_', ' ', $model->app_options)));
                        }
                    ],
                    'app_values',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                if($model->readOnly=='false'){
                                    return Html::a('<span>Update</span>', ['update', 'id'=>$model->id], ['class'=>'btn btn-info btn-xs']);
                                }
                            }
                        ],
                    ],

                ],
            ]); ?>
            <?php yii\widgets\Pjax::end(); ?>
        </div>
    </div>

</div>
