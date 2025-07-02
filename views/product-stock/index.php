<?php

use app\components\DateTimeUtility;
use app\components\Utility;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product Stock');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'Product Stock' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php Utility::gridViewModal($this, $searchModel); ?>


<div class="product-stock-index">

    <?php

    $title = 'Product Stock';
    if (Yii::$app->controller->id == 'report') {
        $colspan = 16;
    } else {
        $colspan = 17;
    }

    echo Utility::gridViewWidget($dataProvider, require(__DIR__.'/_columns.php'), false, $this->title, $colspan, $exportFileName);
    ?>

</div>
