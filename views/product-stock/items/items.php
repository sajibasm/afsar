<?php

use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product Stock');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'stock_items_statement_'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php Utility::gridViewModal($this, $searchModel, 'items/_search'); ?>



<div class="product-stock-index">


    <?php

    $title = 'Stock Statement';
    if(Yii::$app->controller->id=='report'){
        $colspan = 16;
    }else{
        $colspan = 17;
    }

    $button = [];
    echo Utility::gridViewWidget($dataProvider, require(__DIR__.'/_columns.php'), $button, $this->title, $colspan, $exportFileName);

    ?>


</div>
