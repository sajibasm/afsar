<?php

use app\components\DateTimeUtility;
use app\components\Utility;


/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockOutletSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Store Wise Stock');
$this->params['breadcrumbs'][] = $this->title;
Utility::gridViewModal($this, $searchModel);
$exportFileName = 'Store Wise Stock' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');

?>


<div class="product-stock-outlet-index">
    <?php
    if (Yii::$app->controller->id == 'reports') {
        $colSpan = 15;
    } else {
        $colSpan = 15;
    }

    yii\widgets\Pjax::begin(['id' => 'productStockStore']);
    echo Utility::gridViewWidget($dataProvider, require(__DIR__.'/_columns.php'), false, $this->title, $colSpan, $exportFileName);
    yii\widgets\Pjax::end();
    ?>

</div>
