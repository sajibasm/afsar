<?php

use app\components\CustomerUtility;
use app\models\Client;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model app\models\SalesReturn */

$this->title = Yii::t('app', 'Return');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Url::base(true).'/js/Service-Repair-Ajax.js', ['depends'=> JqueryAsset::className()]);
?>
<?php
    Modal::begin([
        'options' => [
            'id' => 'modal',
            'tabindex' => false,
        ],
        'clientOptions'=>[
            'backdrop' => 'static',
            'keyboard' => false,
        ],
        'header' => "<b style='margin:0; padding:0;'> Return Quantity </b>",
        'closeButton' => ['id' => 'close-button'],
        'size'=>Modal::SIZE_DEFAULT

    ]);
    echo '<div id="modalContent"></div>';
    Modal::end();
?>

    <style>
        .panel {
            margin-bottom: 0px !important;
        }

        .break {
            margin-top: 10px;
        }

    </style>

<?php Pjax::begin(['enablePushState' => false, 'id'=>'returnCart',  'timeout' => 10000,]); ?>


    <div class="panel panel-success">
        <div class="panel panel-success">
            <div class="panel-heading">Service Or Repair</div>

            <div class="panel-body">
                <?= $this->render('_customer', ['model'=>$salesReturn,]); ?>
            </div>
        </div>
    </div>


    <div class="panel panel-info break">
        <div class="panel panel-info">
            <div class="panel-heading">Invoice</div>

            <div class="panel-body">
                <?= $this->render('_invoice', ['model'=>$salesReturn, 'account'=>$account,]); ?>
            </div>
        </div>
    </div>


    <div class="panel panel-info break">
        <div class="panel panel-primary">
            <div class="panel-heading">Sold Items</div>

            <div class="panel-body">
                <?= $this->render('salesDetails', ['dataProvider'=>$salesDataProvider,]); ?>
            </div>
        </div>
    </div>


<?php Pjax::end(); ?>