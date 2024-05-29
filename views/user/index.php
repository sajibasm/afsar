<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-index">
 <?php
 Utility::getMessage();
 Utility::gridViewModal($this, $searchModel);
 ?>

    <?php
    $button = 'New User';
    $gridColumns =require(__DIR__.'/_columns.php');
    yii\widgets\Pjax::begin(['id' => 'salesPjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, 10, "users");
    yii\widgets\Pjax::end();
    ?>

</div>

