<?php

    use yii\bootstrap\Modal;
    use yii\helpers\Html;
use yii\grid\GridView;
    use yii\helpers\Url;

    /* @var $this yii\web\View */
/* @var $searchModel app\models\BankSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Bank');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-index">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Product Unit</h3>
            <div class="box-tools pull-right">
                <?= Html::a('Add Bank', ['create'], ['class' => 'btn btn-success', 'data-pjax'=>1])?>
            </div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'bank_name',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id'=>\app\components\Utility::encrypt($model->bank_id)],
                                    [
                                        'class'=>'btn btn-info btn-xs',
                                        'data-toggle'=>'tooltip',
                                        'title'=>Yii::t('app', "Update ".$model->bank_name),
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
