<?php

use app\components\DateTimeUtility;
use app\components\Utility;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStatementOutletSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Store Stock Statement');
$this->params['breadcrumbs'][] = $this->title;

Utility::gridViewModal($this, $searchModel);
$exportFileName = 'Store Stock Statement' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>



<div class="product-statement-store-index">
    <?php
    if (Yii::$app->controller->id == 'reports') {
        $colSpan = 15;
    } else {
        $colSpan = 15;
    }

    yii\widgets\Pjax::begin(['id' => 'StoreStockStatement']);
    echo Utility::gridViewWidget($dataProvider, require(__DIR__.'/_columns.php'), false, $this->title, $colSpan, $exportFileName);
    yii\widgets\Pjax::end();
    ?>

</div>
