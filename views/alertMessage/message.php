<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/22/2015
 * Time: 12:24 AM
 */

    $this->registerJs("
        setTimeout(function() {
            $('#alertMessage').slideToggle(700);
        }, 1000);

    ", \yii\web\View::POS_READY);

    foreach(Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div id="alertMessage"  role="alert" class="alert alert-'.$key.'">'.$message."<a href='#' class='close' data-dismiss='alert'>&times;</a></div>\n";
    }
?>
