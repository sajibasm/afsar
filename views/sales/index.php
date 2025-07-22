<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\grid\GridView;
use kartik\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('app', 'Sales Records');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'sales_statement_' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');

Utility::gridViewModal($this, $searchModel);

$this->registerJs(<<<JS
    $(document).on('editableSuccess.kv', function(event, val, form, data) {
        console.log("editableSuccess.kv triggered");
        console.log("Data:", data);

        if (data && data.output) {
            $(form).closest('.kv-editable-popover').popover('hide');
        }

        if (data && data.message) {
            console.log("Reloading PJAX container...");
            $.pjax.reload({container: '#salesPjaxGridView', async: false});
        }
    });
JS);


$this->registerJs("console.log('Test JS loaded');", \yii\web\View::POS_END);

?>

<div class="sales-index">
    <?php
    Pjax::begin(['id' => 'salesPjaxGridView']);
        echo GridView::widget([
            'id' => 'salesInvoice',
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
                'heading' => '<i class="fas fa-file-invoice"></i> Sales Invoices',
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
                    'filename' => 'Sales_Invoice',
                    'icon' => 'fas fa-file-pdf',
                    'options' => ['title' => 'Sales Invoice'],
                    'config' => [
                        'methods' => [
                            // LEFT | CENTER | RIGHT
                            'SetHeader' => [
                                ['odd' => [
                                    'L' => ['content' => SystemSettings::getStoreName() ?? 'My Store'],
                                    'C' => ['content' => 'Sales Invoice'],
                                    'R' => ['content' => 'Generated: ' . date('d M Y h:i A')],
                                    'line' => true,
                                ],
                                    'even' => [
                                        'L' => ['content' => SystemSettings::getStoreName() ?? 'My Store'],
                                        'C' => ['content' => 'Sales Invoice'],
                                        'R' => ['content' => 'Generated: ' . date('d M Y h:i A')],
                                        'line' => true,
                                    ]]
                            ],
                            'SetFooter' => [
                                ['odd' => [
                                    'L' => ['content' => 'Developed: Asmsajib'],
                                    'C' => ['content' => ''],
                                    'R' => ['content' => 'Page {PAGENO}'],
                                    'line' => true,
                                ],
                                    'even' => [
                                        'L' => ['content' => 'Developed: Asmsajib'],
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
