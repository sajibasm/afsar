<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ReceoncliationTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Reconciliation Types');
$this->params['breadcrumbs'][] = $this->title;

    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();

?>


<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Reconciliation Type</h3>
        <div class="box-tools pull-right">
            <?= Html::a('Add Reconciliation Type', ['create'], ['class' => 'btn btn-info', 'data-pjax'=>1])?>
        </div>
    </div>
    <div class="box-body">
        <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'name',
                'show_invoice',
                'status',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{update}',
                    'header'=>'Action'
                ],
            ],
        ]); ?>
        <?php yii\widgets\Pjax::end(); ?>
    </div>
</div>




