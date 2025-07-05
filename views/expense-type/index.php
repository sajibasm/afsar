<?php


/* @var $this yii\web\View */
/* @var $searchModel app\models\ExpenseTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Expense Types');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Utility::gridViewModal($this, $searchModel); ?>

<div class="expense-type-index">
    <?php
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'header' => '#',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'expense_type_name',
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'attribute' => 'expense_type_status',
            'format' => 'raw',
            'hAlign' => GridView::ALIGN_CENTER,

            'value' => function ($model) {
                return \app\components\BadgeHelper::render($model->expense_type_status);
            },
        ],


        [
            'class' => 'kartik\grid\ActionColumn',
            'hidden' => Yii::$app->controller->id == 'reports' ? true : false,
            'vAlign' => GridView::ALIGN_RIGHT,
            'hiddenFromExport' => true,
            'urlCreator' => function ($action, $model, $key, $index) {
                return Url::to([$action, 'id' => \app\components\Utility::encrypt($key)]);
            },
            'template' => \mdm\admin\components\Helper::filterActionColumn('{update}'),
            'buttons' => [
                'update' => function ($url, $model) {
                    return \app\components\ButtonHelper::actionButton('update', $url, [
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update'),
                    ]);
                },
            ]
        ],
    ];

    yii\widgets\Pjax::begin(['id' => 'expenseAjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, 4, 'expense_payment_type');
    yii\widgets\Pjax::end();
    ?>
</div>
