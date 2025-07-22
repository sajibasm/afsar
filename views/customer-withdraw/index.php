<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\CustomerWithdraw;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerWithdrawSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment Refund Records');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Utility::gridViewModal($this, $searchModel); ?>

<div class="customer-withdraw-index">

    <?php
    Pjax::begin(['id' => 'customerWithdrawPjaxGridView']);
    echo GridView::widget([
        'id' => 'customerWithdrawPjaxGridView',
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel ?? null, // Optional, if using search
        'columns' => require(__DIR__ . '/_columns.php'), // ⬅️ Include your columns config
        'pjax' => true,
        //        'pjaxSettings' => [
        //            'neverTimeout' => true,
        //            'options' => ['enablePushState' => false], // optional
        //        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => '<i class="fas fa-receipt"></i> Payment Refund Records',
        ],
        'toolbar' => [
            ['content' =>
                Html::button('<i class="fas fa-filter"></i> Filter', [
                    'type' => 'button',
                    'data-toggle' => 'modal',
                    'data-target' => '#filter',
                    'title' => Yii::t('app', 'Filter'),
                    'class' => 'btn btn-info',
                ]) . ' ' .

                Html::a('<i class="fas fa-sync-alt"></i> Reload', Yii::$app->controller->action->id, [
                    'class' => 'btn btn-default',
                    'title' => 'Reload Grid',
                    'data-pjax' => 1,
                ])
            ],
            '{export}',
            '{toggleData}',
        ],

        'exportConfig' => [
            GridView::PDF => [
                'label' => 'PDF',
                'filename' => 'payment_refund_records',
                'icon' => 'fas fa-file-pdf',
                'options' => ['title' => 'Payment Refund Records'],
                'config' => [
                    'format' => 'A4-L', // 'A4-P' → A4 Portrait, 'A4-L' → A4 Landscape, 'LETTER', 'LEGAL', etc. also supported.

                    'methods' => [
                        // LEFT | CENTER | RIGHT
                        'SetHeader' => [
                            ['odd' => [
                                'L' => ['content' => SystemSettings::Company() ?? 'My Store'],
                                'C' => ['content' => 'Payment Refund Records'],
                                'R' => ['content' => 'Generated: ' .DateTimeUtility::getDate('Now', 'd-m-Y h:i:s A')],
                                'line' => true,
                            ],
                                'even' => [
                                    'L' => ['content' => SystemSettings::Company() ?? 'My Store'],
                                    'C' => ['content' => 'Payment Refund Records'],
                                    'R' => ['content' => 'Generated: ' .DateTimeUtility::getDate('Now', 'd-m-Y h:i:s A')],
                                    'line' => true,
                                ]]
                        ],
                        'SetFooter' => [
                            ['odd' => [
                                'L' => ['content' => 'Developed: '. SystemSettings::DevelopBy()],
                                'C' => ['content' => ''],
                                'R' => ['content' => 'Page {PAGENO}'],
                                'line' => true,
                            ],
                                'even' => [
                                    'L' => ['content' => 'Developed: '. SystemSettings::DevelopBy()],
                                    'C' => ['content' => ''],
                                    'R' => ['content' => 'Page {PAGENO}'],
                                    'line' => true,
                                ]]
                        ]                    ],
                ],
            ],
            GridView::CSV => [
                'label' => 'CSV',
                'filename' => 'Sales_Invoice',
                'icon' => 'fas fa-file-csv',
            ],
            GridView::EXCEL => [
                'label' => 'Excel',
                'filename' => 'Sales_Invoice',
                'icon' => 'fas fa-file-excel',
            ],
        ],
        'export' => [
            'fontAwesome' => true, // Enables font-awesome icons
            'showConfirmAlert' => false,
            'enableFormatter' => true, // Already added in 'export'
            'exportConversions' => false, // ✅ Add this line
            'target' => GridView::TARGET_BLANK,
            'label' => 'Export',
            'menuOptions' => ['class' => 'dropdown-menu dropdown-menu-right'],
            'dropdownOptions' => [
                'label' => '<i class="fas fa-file-export"></i> Export',
                'class' => 'btn btn-outline-dark',
                'encodeLabel' => false, // Allow HTML in label
            ],

        ],

        'hover' => true,
        'striped' => true,
        'bordered' => true,
        'condensed' => true,
        'responsive' => true,
        'responsiveWrap' => false,
        'persistResize' => false,
        'floatHeader' => true,
        'showPageSummary' => true,
    ]);
    Pjax::end();

    ?>


</div>
