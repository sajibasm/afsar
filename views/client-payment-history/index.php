<?php

use app\components\BadgeHelper;
use app\components\ButtonHelper;
use app\components\SystemSettings;
use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\ClientPaymentHistory;
use kartik\grid\GridView;
use mdm\admin\components\Helper;
use yii\bootstrap\Alert;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientPaymentHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customer Payment History');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'customer'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>
<div class="client-payment-history-index">

    <?php Utility::gridViewModal($this, $searchModel); ?>

    <?php

    Pjax::begin(['id' => 'customerPaymentHistoryGrid']);
    echo GridView::widget([
        'id' => 'customerPaymentHistoryGrid',
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
                'filename' => 'Customer_Payment',
                'icon' => 'fas fa-file-pdf',
                'options' => ['title' => 'Customer Payment Records'],
                'config' => [
                    'methods' => [
                        // LEFT | CENTER | RIGHT
                        'SetHeader' => [
                            ['odd' => [
                                'L' => ['content' => SystemSettings::getStoreName() ?? 'My Store'],
                                'C' => ['content' => 'Customer Payment Records'],
                                'R' => ['content' => 'Generated: ' . DateTimeUtility::getDate('NOW', 'd-m-Y h:i:s A')],
                                'line' => true,
                            ],
                                'even' => [
                                    'L' => ['content' => SystemSettings::getStoreName() ?? 'My Store'],
                                    'C' => ['content' => 'Customer Payment Records'],
                                    'R' => ['content' => 'Generated: ' . DateTimeUtility::getDate('NOW', 'd-m-Y h:i:s A')],
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
                        ]],
                ],
            ],
            GridView::CSV => [
                'label' => 'CSV',
                'filename' => 'Customer_Payment',
                'icon' => 'fas fa-file-csv',
            ],
            GridView::EXCEL => [
                'label' => 'Excel',
                'filename' => 'Customer_Payment',
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
