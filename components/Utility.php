<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */

namespace app\components;


use Faker\Provider\bn_BD\Utils;
use kartik\grid\GridView;
use Yii;
use yii\base\Model;
use yii\bootstrap\Modal;
use yii\helpers\Html;

class Utility
{
    public static function debug($object, $exist = true)
    {
        echo "<pre>";
        print_r($object);
        echo "</pre>";

        if ($exist) {
            die();
        }
    }

    public static function serializeModel(Model $model, $base64Encode = false)
    {
        if ($base64Encode) {
            return base64_encode(serialize($model->getAttributes()));
        } else {
            return serialize($model->getAttributes());
        }
    }

    public static function unserializeModel($serialize, $base64Decode = false)
    {
        if ($base64Decode) {
            return unserialize(base64_decode($serialize));
        } else {
            return unserialize($serialize);
        }
    }

    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public static function encrypt($data)
    {
        return self::base64url_encode(Yii::$app->security->encryptByKey($data, self::getParam('secretKey')));
    }

    public static function decrypt(&$data)
    {
        return Yii::$app->security->decryptByKey(self::base64url_decode($data), self::getParam('secretKey'));
    }

    public static function getParam($key)
    {
        return Yii::$app->params[$key];
    }

    public static function asDecimal($value, $decimal = 0)
    {
        $formatter = \Yii::$app->formatter;
        return $formatter->asDecimal($value, $decimal);
    }

    public static function asCurrency($value, $currency = null, $options = [], $textOptions = [])
    {
        $formatter = \Yii::$app->formatter;
        return $formatter->asCurrency($value, $currency = null, $options = [], $textOptions = []);
    }

    public static function asCurrencyWithCode($value, $currency = null, $options = [], $textOptions = [])
    {
        return Utility::asCurrency($value, $currency = null, $options = [], $textOptions = []) . ' ' . Yii::$app->params['currency'];
    }


    public static function getMessage()
    {
        return FlashMessage::getMessage();
    }

    public static function genInvoice($prefix)
    {
        return strtoupper(uniqid(str_replace('-', '', $prefix . '-')));
    }

    public static function getModel($title = 'Details', $size = Modal::SIZE_LARGE)
    {

        Modal::begin([
            'options' => [
                'id' => 'modal',
                'tabindex' => false,
            ],
            'clientOptions' => [
                'backdrop' => 'static',
                'keyboard' => false,
            ],
            'header' => "<b style='margin:0; padding:0;'>{$title}</b>",
            'closeButton' => ['id' => 'close-button'],
            'size' => $size
        ]);

        echo '<div id="modalContent"></div>';

        Modal::end();
    }

    public static function getApprovalModel($title = 'Approved', $size = Modal::SIZE_LARGE)
    {

        Modal::begin([
            'options' => [
                'id' => 'approvedModal',
                'tabindex' => false,
            ],
            'clientOptions' => [
                'backdrop' => 'static',
                'keyboard' => false,
            ],
            'header' => "<b style='margin:0; padding:0;'>Approval Confirmation</b>",
            'closeButton' => ['id' => 'close-button'],
            'size' => Modal::SIZE_DEFAULT
        ]);

        echo '<div id="modalConfirmation"></div>';

        Modal::end();


        Modal::begin([
            'options' => [
                'id' => 'modal',
                'tabindex' => false,
            ],
            'clientOptions' => [
                'backdrop' => 'static',
                'keyboard' => false,
            ],
            'header' => "<b style='margin:0; padding:0;'>Details</b>",
            'closeButton' => ['id' => 'close-button'],
            'size' => Modal::SIZE_LARGE
        ]);

        echo '<div id="modalContent"></div>';

        Modal::end();
    }

    public static function gridViewModal($controller, $searchModel, $renderFile = '_search')
    {


        Modal::begin([
            'options' => [
                'id' => 'approvedModal',
                'tabindex' => true,
            ],
            'clientOptions' => [
                'backdrop' => 'static',
                'keyboard' => false,
            ],
            'header' => "<b style='margin:0; padding:0;'>Approval Confirmation</b>",
            'closeButton' => ['id' => 'close-button'],
            'size' => Modal::SIZE_DEFAULT
        ]);

        echo '<div id="modalConfirmation"></div>';

        Modal::end();


        Modal::begin([
            'options' => [
                'id' => 'modal',
                'tabindex' => false,
            ],
            'clientOptions' => [
                'backdrop' => 'static',
                'keyboard' => false,
            ],
            'header' => "<b style='margin:0; padding:0;'>Details</b>",
            'closeButton' => ['id' => 'close-button'],
            'size' => Modal::SIZE_LARGE
        ]);

        echo '<div id="modalContent"></div>';

        Modal::end();


        Modal::begin([
            'header' => '<h4 style="margin:0; padding:0">Search</h4>',
            'id' => 'stock-filter',
            'size' => 'modal-medium',
            'options' => [
                'id' => 'filter',
                'tabindex' => false // important for Select2 to work properly
            ],
        ]);

        echo $controller->render($renderFile, ['model' => $searchModel]);

        Modal::end();
    }

    public static function gridViewWidget($dataProvider, $gridColumns, $addButtons = 'New', $title = 'Statement', $colSpan, $exportFileName = 'statement', $showHeader = true, $showExport = true, $filter = true, $orientation = "A4-L")
    {
        $button = '';
        if (is_array($addButtons) && Yii::$app->controller->id != 'reports') {
            foreach ($addButtons as $btn) {
                $button .= $btn . ' ';
            }
        } elseif (Yii::$app->controller->id != 'reports' && !empty($addButtons) && $addButtons) {
            $button = Html::a(Yii::t('app', $addButtons), ['create'], ['class' => 'btn btn-info', 'data-pjax' => 0]);
        }

        $reloadUrl = [Yii::$app->controller->id . '/' . Yii::$app->controller->action->id];

        $defaultExportConfig = [
            GridView::PDF => [
                'hashExportConfig'=>false,
                'label' => Yii::t('app', 'PDF'),
                'icon' => 'text-danger fa fa-file-pdf-o',
                'iconOptions' => ['class' => 'text-danger'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('app', $exportFileName),
                'alertMsg' => Yii::t('app', 'The PDF export file will be generated for download.'),
                'options' => ['title' => Yii::t('app', 'Portable Document Format')],
                'mime' => 'application/pdf',
                'config' => [
                    'mode' => 'utf-8',
                    'format' => $orientation,
                    'destination' => 'D',
                    'marginTop' => 5,
                    'marginBottom' => 2,
                    'cssInline' => '.kv-wrap{padding:20px;}' .
                        '.kv-align-center{text-align:center;}' .
                        '.kv-align-left{text-align:left;}' .
                        '.kv-align-right{text-align:right;}' .
                        '.kv-align-top{vertical-align:top!important;}' .
                        '.kv-align-bottom{vertical-align:bottom!important;}' .
                        '.kv-align-middle{vertical-align:middle!important;}' .
                        '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                        '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                        '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}',
                    'methods' => [
                        'SetHeader' => [
                            ['odd' => 'odd', 'even' => "even"]
                        ],
                        'SetFooter' => [
                            ['odd' => 'odd', 'even' => "even"]
                        ],
                    ],
                    'options' => [
                        'title' => $title,
                        'subject' => Yii::t('app', 'PDF export generating by Axial'),
                        'keywords' => Yii::t('app', 'axial, pdf')
                    ],
                    'contentBefore' => '<p><h3 style="text-align: center">' . SystemSettings::getStoreName() . '</h3></p>',
                ]
            ],

            GridView::EXCEL => [
                'label' => Yii::t('app', 'Excel'),
                'icon' => 'text-success fa fa-file-excel-o',
                'iconOptions' => ['class' => 'text-success'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('app', $exportFileName),
                'alertMsg' => Yii::t('app', 'The EXCEL export file will be generated for download.'),
                'options' => ['title' => Yii::t('app', 'Microsoft Excel 95+')],
                'mime' => 'application/vnd.ms-excel',
                'config' => [
                    'worksheet' => Yii::t('app', 'ExportWorksheet'),
                    'cssFile' => ''
                ]
            ],

        ];

        $gridView = [
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'layout' => '{summary}{errors}{items}{sorter}{pager}',
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'exportConfig' => $defaultExportConfig,
            'headerRowOptions' => ['class' => 'kartik-sheet-style'],
            'filterRowOptions' => ['class' => 'kartik-sheet-style'],
            'resizeStorageKey' => Yii::$app->user->getId() . '-' . time(),
            'beforeHeader' => [
                $showHeader ? [
                    'columns' => [
                        ['content' => $title, 'options' => ['colspan' => $colSpan, 'class' => 'text-center default']],
                    ]
                ] : []
            ],

            'export' => [
                'fontAwesome' => true,
                'target' => GridView::TARGET_SELF,
                'message' => [
                    'allowPopups' => 'Disable any popup blockers in your browser to ensure proper download.',
                    'confirmDownload' => 'Ok to proceed and download?',
                    'downloadProgress' => 'Generating file. Please wait...'
                ]

            ],

            'toolbar' => [
                $button,
                $filter ? ['content' =>
                    Html::button('<i class="glyphicon glyphicon-filter"></i>', [
                        'type' => 'button',
                        'data-toggle' => 'modal',
                        'data-target' => '#filter',
                        'title' => Yii::t('app', 'Filter'),
                        'class' => 'btn btn-default'
                    ]) . ' ' .
                    Html::a('<i class="glyphicon glyphicon-repeat"></i> ', $reloadUrl, ['class' => 'btn btn-info'])
                ] : [],

                "{export} {toggleData}"
            ],

            'toggleDataOptions' => [
                'all' => [
                    'icon' => 'resize-full',
                    'label' => Yii::t('app', 'All'),
                    'class' => 'btn btn-default',
                    'title' => 'Show all data'
                ],
                'page' => [
                    'icon' => 'resize-small',
                    'label' => Yii::t('app', 'Page'),
                    'class' => 'btn btn-default',
                    'title' => 'Show first page data'
                ],
            ],

            'panel' => [
                'type' => Yii::$app->params['gridviewHeaderColor'],
            ],

            'persistResize' => true,
            'bordered' => true,
            'bootstrap' => true,
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'hover' => true,
            'showPageSummary' => true,

            'pager' => [
                'firstPageLabel' => 'First',
                'lastPageLabel' => 'Last'
            ],

            'pjax' => false,
            'pjaxSettings' => [
                'neverTimeout' => false,
            ],
        ];

        return GridView::widget($gridView);
    }

}
