<?php

use app\components\CommonUtility;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DepositBookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Bank Statement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bank Deposit'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deposit-book-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,

        'layout' => '{summary}{items}{pager}',
        'showFooter' => true,
        'footerRowOptions'=>['style'=>'font-weight:bold;text-decoration: underline;'],

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
//            [
//                'header'=>'Customer',
//                'value'=>function($model){
//                    return $model->customer->client_name;
//                }
//            ],

            [
                'header'=>'Bank ( Branch )',
                'value'=>function($model){
                    return $model->branch->bank->bank_name.' ( '.$model->branch->branch_name.' )';
                }
            ],
            [
                'header'=>'Type',
                'value'=>function($model) {
                    return $model->paymentType->payment_type_name;
                }
            ],

            'remarks',
            'reference_id',
            'source',
            [
                'attribute'=>'deposit_in',
                'value'=>function($model){
                    return $model->deposit_in.' '.Yii::$app->params['currency'];
                },
                'footer'=> CommonUtility::pageTotal($dataProvider->models,'deposit_in').' '.Yii::$app->params['currency'],

            ],
            [
                'attribute'=>'deposit_out',
                'value'=>function($model){
                    return $model->deposit_out.' '.Yii::$app->params['currency'];
                },
                'footer'=> CommonUtility::pageTotal($dataProvider->models,'deposit_out').' '.Yii::$app->params['currency'],

            ],
            'created_at',
            // 'updated_at',
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
