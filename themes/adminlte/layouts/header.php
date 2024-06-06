<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this \yii\web\View */
/* @var $content string */
$this->registerJs("var canonicalUrl='".Url::canonical()."'", View::POS_HEAD, 'canonical');
$this->registerJs("var baseUrl='".Url::base(true)."/'", View::POS_HEAD, 'base');
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini"></span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">


<!--                <li class="dropdown notifications-menu">-->
<!--                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">-->
<!--                        <i class="fa fa-bell-o"></i>-->
<!--                        <span class="label label-warning">10</span>-->
<!--                    </a>-->
<!--                    <ul class="dropdown-menu">-->
<!--                        <li class="header">You have 10 notifications</li>-->
<!--                        <li>-->
<!--                             inner menu: contains the actual data -->
<!--                            <ul class="menu">-->
<!--                                <li>-->
<!--                                    <a href="#">-->
<!--                                        <i class="fa fa-users text-aqua"></i> 5 new members joined today-->
<!--                                    </a>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <a href="#">-->
<!--                                        <i class="fa fa-warning text-yellow"></i> Very long description here that may-->
<!--                                        not fit into the page and may cause design problems-->
<!--                                    </a>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <a href="#">-->
<!--                                        <i class="fa fa-users text-red"></i> 5 new members joined-->
<!--                                    </a>-->
<!--                                </li>-->
<!---->
<!--                                <li>-->
<!--                                    <a href="#">-->
<!--                                        <i class="fa fa-shopping-cart text-green"></i> 25 sales made-->
<!--                                    </a>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <a href="#">-->
<!--                                        <i class="fa fa-user text-red"></i> You changed your username-->
<!--                                    </a>-->
<!--                                </li>-->
<!--                            </ul>-->
<!--                        </li>-->
<!--                        <li class="footer"><a href="#">View all</a></li>-->
<!--                    </ul>-->
<!--                </li>-->

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= Yii::getAlias('@web').'/uploads/employee/user.png' ?>" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= isset(Yii::$app->user->identity)?ucwords(Yii::$app->user->identity->username):''; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= Yii::getAlias('@web').'/uploads/employee/user.png' ?>" class="img-circle"
                                 alt="User Image"/>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?= Html::a(
                                    'Change Password',
                                    ['/admin/user/change-password'],
                                    ['class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
<!--                <li>-->
<!--                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>-->
<!--                </li>-->
            </ul>

        </div>
    </nav>
</header>
