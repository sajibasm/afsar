<?php

    use yii\helpers\Html;

use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
    /* @var $model app\models\ProductStockItemsDraft */

    $this->title = Yii::t('app', 'New Sell');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;

    $this->registerJs("var ajaxRequestUrl='".Url::base(true).'/'.Yii::$app->controller->id.'/check-available-product'."';", View::POS_END, 'checkAvailableProduct');

    $this->registerJsFile(Url::base(true).'/lib/js/salesAjax.js', ['depends'=>\yii\web\JqueryAsset::className()]);
    //$this->registerJsFile(Url::base(true).'/js/salesDraftUpdate.js', ['depends'=>\yii\web\JqueryAsset::className()]);

?>

    <div class="product-sales-items-draft-create">

        <div class="row">

            <div class="col-md-10">
                <?= $this->render('_product', ['model'=>$salesDraft, 'dataProvider' => $salesDraftDataProvider,]) ?>
                <?= $this->render('details', ['dataProvider'=>$salesDraftDataProvider,]) ?>
            </div>

            <div class="col-md-2">

            </div>

        </div>

    </div>