<?php


/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Payment Types');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Utility::gridViewModal($this, $searchModel); ?>

<div class="payment-type-index">
    <?php
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'header' => '#',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'payment_type_name',
            'hAlign' => GridView::ALIGN_CENTER,
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'type',
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'class' => 'kartik\grid\ActionColumn',
            'hidden' => Yii::$app->controller->id == 'reports' ? true : false,
            'vAlign' => GridView::ALIGN_RIGHT,
            'hAlign' => GridView::ALIGN_CENTER,
            'hiddenFromExport' => true,

            'headerOptions' => ['style' => 'text-align: center; width:50px;'],
            'contentOptions' => ['style' => 'text-align: center;'],
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return \app\components\ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->payment_type_id)]), [
                        'class' => '',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update'),
                    ]);
                },
            ],
        ],
    ];

    yii\widgets\Pjax::begin(['id' => 'paymentAjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, 4, 'payment_type');
    yii\widgets\Pjax::end();
    ?>
</div>
