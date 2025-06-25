<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\SalesReturn;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesReturnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Return-Service Statement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'sales_statement_'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>


<?php Utility::gridViewModal($this, $searchModel); ?>


<div class="sales-return-index">

    <?php

        if(Yii::$app->controller->id=='report'){
            $colspan = 10;
        }else{
            $colspan = 10;
        }

    yii\widgets\Pjax::begin(['id'=>'salesReturn']);
    echo Utility::gridViewWidget($dataProvider, require(__DIR__.'/_columns.php'), false, $this->title, $colspan, $exportFileName);
    yii\widgets\Pjax::end();

    ?>

</div>
