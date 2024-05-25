<?php

use app\components\Utility;
use yii\bootstrap\Modal;
    use yii\helpers\Html;
use yii\grid\GridView;
    use yii\helpers\Url;

    /* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customer');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Customer</h3>
            <div class="box-tools pull-right">
                <?= Html::a('Add Customer', ['create'], ['class' => 'btn btn-info', 'data-pjax'=>1])?>
            </div>
        </div>
        <div class="box-body" id="customer_from">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'client_name',
                    //'email',
                    'clientCity.city_name',
                    'outlet.name',
                    'client_address1',
                    'client_address2',
                    'client_contact_number',
                    'client_contact_person',
                    'client_contact_person_number',
                    'client_type',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['client/update', 'id'=> Utility::encrypt($model->client_id)], ['class'=>'btn btn-info btn-xs', 'data-ajax'=>0]);
                            }
                        ],
                    ],
                ],
            ]); ?>
            <?php yii\widgets\Pjax::end(); ?>
        </div>
    </div>

</div>
