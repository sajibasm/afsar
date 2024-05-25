<?php

use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStatementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product Statements');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'product_statement' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php

Utility::gridViewModal($this, $searchModel);
//Utility::getMessage();
?>


<div class="product-statement-index">

    <?php

    $gridColumns = [

        ['class' => 'kartik\grid\SerialColumn'],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'created_at',
            'pageSummary' => false,
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign' => GridView::ALIGN_LEFT,
            'header' => 'Item',
            'value' => function ($model) {
                return $model->item->item_name;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign' => GridView::ALIGN_LEFT,
            'header' => 'Brand',
            'value' => function ($model) {
                return $model->brand->brand_name;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign' => GridView::ALIGN_LEFT,
            'header' => 'Size',
            'value' => function ($model) {
                return $model->size->size_name;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign' => GridView::ALIGN_LEFT,
            'header' => 'Type',
            'value' => function ($model) {
                return $model->type;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Remarks(Ref)',
            'attribute' => 'remarks',
            //'pageSummary' => false,
            'hAlign' => GridView::ALIGN_CENTER,
            'pageSummary' => 'Total',
            'value' => function ($model, $key, $value) {
                return $model->remarks . "(" . $model->reference_id . ")";
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign' => GridView::ALIGN_LEFT,
            'header' => 'Quantity',
            'pageSummary' => true,
            'format' => ['decimal', 0],
            'value' => function ($model) {
                return $model->quantity;
            },

        ],

//            [
//                'class' => 'kartik\grid\CheckboxColumn',
//                //'options'=>['class'=>'skip-export']
//            ],

    ];

    $params = Yii::$app->request->get("ProductStatementSearch");
    $title = '';
    $header = ' Product Statement ';

    if (Yii::$app->controller->id == 'report') {
        $colspan = 9;
    } else {
        $colspan = 9;
    }

    $button = '';

    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);


    ?>

</div>
