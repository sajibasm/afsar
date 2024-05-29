<?php


/* @var $this yii\web\View */
/* @var $searchModel app\models\LcPaymentTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'LC Payment Type');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>


<div class="lc-payment-type-index">

    <?php

    $button = 'New LC Payment Type';

    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'header' => '#',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'lc_payment_type_name',
            'hAlign' => GridView::ALIGN_CENTER,
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'lc_payment_type_status',
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'class' => 'kartik\grid\ActionColumn',
            'hidden' => Yii::$app->controller->id == 'reports' ? true : false,
            'vAlign' => GridView::ALIGN_RIGHT,
            'hiddenFromExport' => true,
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['lc-payment-type/update', 'id' => Utility::encrypt($model->expense_type_id)]), [
                        'class' => 'btn btn-info btn-xs',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update Type'),
                    ]);
                }
            ],
        ],
    ];

    yii\widgets\Pjax::begin(['id' => 'expenseAjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, 4, 'lc_payment_type');
    yii\widgets\Pjax::end();
    ?>
</div>
