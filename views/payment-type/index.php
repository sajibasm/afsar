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

<?php

Utility::gridViewModal($this, $searchModel);

Utility::getMessage();
?>

<div class="payment-type-index">
    <?php

    $button = 'New Payment Type';

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
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['payment-type/update', 'id' => Utility::encrypt($model->payment_type_id)]), [
                        'class' => 'btn btn-info btn-xs',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update Type'),
                    ]);
                }
            ],
        ],
    ];

    yii\widgets\Pjax::begin(['id' => 'withdrawAjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, 4, 'payment_type');
    yii\widgets\Pjax::end();


    ?>
</div>
