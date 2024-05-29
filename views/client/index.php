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

<?php

Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>


<div class="customer-withdraw-index">

    <?php Pjax::begin(); ?>
    <?php

    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Outlet',
            'attribute' => 'outlet.name',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'client_type',
            'hiddenFromExport' => true,
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
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update', 'id' => Utility::encrypt($model->client_id)]), [
                        'class' => 'btn btn-info btn-xs',
                        'data-pjax' => 0
                    ]);
                }
            ],

        ],

    ];

    if (Yii::$app->controller->id == 'reports') {
        $colspan = 8;
    } else {
        $colspan = 8;
    }

    $button = "New Customer";

    yii\widgets\Pjax::begin(['id' => 'customerWithdrawPjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, "customer");
    yii\widgets\Pjax::end();
    ?>

    <?php Pjax::end(); ?>

</div>