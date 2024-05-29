<?php
/* @var $this yii\web\View */

use app\components\Utility;
use yii\helpers\BaseUrl;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

//\app\assets\ChartAsset::register($this);

$defaultOutlet = (!empty($outlets)) ? $outlets[0]->outletId : '';
$this->title = 'ASL Inventory';
$this->registerJs("var dailySummeryUrl='" . Url::base(true) . '/' . Yii::$app->controller->id . '/daily-summery' . "';", View::POS_BEGIN, 'dailySummery');
$this->registerJs("var defaultOutlet='" . Utility::encrypt($defaultOutlet) . "';", View::POS_END, 'defaultOutlet');
$this->registerJs("var dashboardUrl='" . Url::base(true) . '/' . Yii::$app->controller->id . '/' . "';", View::POS_BEGIN, 'dashboardUrl');
$this->registerJsFile('@web/lib/amcharts/4.8.9/core.js', ['position' => View::POS_END, 'depends' => [JqueryAsset::className()]]);
$this->registerJsFile('@web/lib/amcharts/4.8.9/charts.js', ['position' => View::POS_END, 'depends' => [JqueryAsset::className()]]);
$this->registerJsFile('@web/lib/amcharts/4.8.9/themes/dataviz.js', ['position' => View::POS_END, 'depends' => [JqueryAsset::className()]]);
$this->registerJsFile('@web/lib/amcharts/4.8.9/themes/material.js', ['position' => View::POS_END, 'depends' => [JqueryAsset::className()]]);
$this->registerJsFile('@web/lib/amcharts/4.8.9/themes/animated.js', ['position' => View::POS_END, 'depends' => [JqueryAsset::className()]]);
$this->registerJsFile('@web/lib/js/site.js', ['position' => View::POS_END, 'depends' => [JqueryAsset::className()]]);

?>

<link rel="stylesheet" type="text/css" href=<?= BaseUrl::base(true) . '/lib/ionicons/docs/v2/css/ionicons.min.css' ?>>
<style>
    #chartdiv {
        width: 100%;
        height: 500px;
    }

    #chartdiv2 {
        width: 100%;
        height: 450px;
    }

    #chartdiv3 {
        width: 100%;
        height: 450px;
    }

    #chartdiv4 {
        width: 100%;
        height: 315px;
    }

    #chartdiv5 {
        width: 100%;
        height: 300px;
    }

    #chartdiv6 {
        width: 100%;
        height: 300px;
    }

</style>

<?php Utility::getMessage(); ?>

<nav class="navbar navbar-default">
    <div class="container-fluid">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= Url::to(['/'])?>" title="ShareTrip Inc">
                <img style="height: 40px; width: 40px; margin-top: -10px" src="<?= Yii::getAlias('@web/images/logo.png')?>" alt="">
            </a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav" id="dashboardUrlList">
                <?php foreach ($outlets as $key => $outlet): ?>
                    <li title="<?= $outlet->outletDetail->name ?>"
                        data-id="<?= Utility::encrypt($outlet->outletId) ?>"
                        id="getOutletId_<?= $outlet->outletId ?>"
                        class="<?= ($key == 0) ? 'active' : '' ?> outlet_menu"><a
                            style="cursor: pointer"><?= $outlet->outletDetail->name ?> <span class="sr-only">(current)</span></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<div class="site-index">

    <div class="row">

        <div class="col-md-8">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-shopping-cart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sales</span>
                                <span id="dailySales" class="info-box-number"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-table"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Dues</span>
                                <span id="dailyDues" class="info-box-number"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="clearfix visible-sm-block"></div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-money"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sales(PAID)</span>
                                <span id="dailySalesCash" class="info-box-number"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-arrow-circle-left"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sales-Return</span>
                                <span id="dailySalesReturn" class="info-box-number"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-bar-chart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Due Rec.</span>
                                <span id="dailyDueCollection" class="info-box-number"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-bars"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Expense</span>
                                <span id="dailyExpense" class="info-box-number"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-hand-lizard-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cash-Hand</span>
                                <span id="dailyCashHand" class="info-box-number"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-arrow-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Withdraw</span>
                                <span id="dailyWithdraw" class="info-box-number"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">

                            <span class="info-box-icon bg-green"><i class="fa fa-angle-double-down"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cash-In</span>
                                <span id="dailyCashIn" class="info-box-number"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-angle-double-up"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Cash-Out</span>
                                <span id="dailyCashOut" class="info-box-number"></span>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix visible-sm-block"></div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-angle-double-down"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Bank-In</span>
                                <span id="dailyBankIn" class="info-box-number"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-angle-double-down"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Bank-Out</span>
                                <span id="dailyBankOut" class="info-box-number"></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                </div>

                <div class="box-footer"></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Store Sales</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div id="chartdiv4"></div>

                </div>

                <div class="box-footer"></div>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Sales</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div id="chartdiv5"></div>

                </div>

                <div class="box-footer"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Expense</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div id="chartdiv6"></div>

                </div>

                <div class="box-footer"></div>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Cash Analytics</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="chartdiv2"></div>
                        </div>
                    </div>

                </div>
                <div class="box-footer"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Bank Analytics</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="chartdiv3"></div>
                        </div>
                    </div>
                </div>

                <div class="box-footer"></div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Sales Growth</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="chartdiv"></div>
                        </div>
                    </div>
                </div>

                <div class="box-footer"></div>
            </div>
        </div>
    </div>

</div>
