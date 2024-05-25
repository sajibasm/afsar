<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21-Nov-17
 * Time: 4:34 PM
 */
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    header("location: ".$actual_link.'web');
?>