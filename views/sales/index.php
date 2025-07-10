<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('app', 'Sales Records');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'sales_statement_' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');

Utility::gridViewModal($this, $searchModel);

?>

<div class="sales-index">
    <?php
    if (Yii::$app->controller->id == 'reports') {
        $colSpan = 15;
    } else {
        $colSpan = 18;
    }

    yii\widgets\Pjax::begin(['id' => 'salesPjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, require(__DIR__.'/_columns.php'), false, $this->title, $colSpan, $exportFileName);
    yii\widgets\Pjax::end();
    ?>

</div>
