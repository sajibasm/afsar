<?php

use app\components\Utility;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customer');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Utility::gridViewModal($this, $searchModel); ?>

<div class="customer-withdraw-index">

    <?php Pjax::begin(); ?>
    <?php

    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'outlet.name',
        ],

        [
            'attribute' => 'client_type',
            'format' => 'raw',
            'hiddenFromExport' => true,
            'value' => function ($model) {
                return \app\components\BadgeHelper::render($model->client_type);
            },
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'client_name',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'clientCity.city_name',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header'=>'Address',
            'attribute' => 'client_address1',
            'value' => function ($model) {
                return $model->client_address1.' '.$model->client_address2;
            }
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'client_contact_number',
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'client_contact_person',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'client_contact_person_number',
        ],

        [
            'class' => 'kartik\grid\ActionColumn',
            'hiddenFromExport' => true,
            'width' => '120px',
            'vAlign' => GridView::ALIGN_RIGHT,
            'hAlign' => GridView::ALIGN_CENTER,
            'headerOptions' => ['style' => 'text-align: center; width:50px;'],
            'contentOptions' => ['style' => 'text-align: center;'],
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return \app\components\ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->client_id)]), [
                        'class' => '',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update Client# ' . $model->client_id),
                    ]);
                },
            ],

        ],

    ];

    if (Yii::$app->controller->id == 'reports') {
        $colspan = 8;
    } else {
        $colspan = 8;
    }

    yii\widgets\Pjax::begin(['id' => 'customerWithdrawPjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, "customer");
    yii\widgets\Pjax::end();
    ?>

    <?php Pjax::end(); ?>

</div>