<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\OutletSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;

$this->title = Yii::t('app', 'Outlets');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>

<div class="outlet-index">
    <?php
    if(Yii::$app->controller->id=='report'){
        $colspan = 3;
    }else{
        $colspan = 3;
    }

    $button = 'New Outlet';
    yii\widgets\Pjax::begin(['id'=>'outletAjax']);
    echo Utility::gridViewWidget($dataProvider, require(__DIR__.'/_columns.php'), $button, $this->title, $colspan, 'outlet');
    yii\widgets\Pjax::end();
    ?>



</div>
