<?php
$secret_key = '0MkOLqXP0qxzse19AHF1NRPM6LP23lO1QfTGSpW7y/CBanDbRS7LqDMVHMuzMM6J45H6eqhndNJTuUN5oZQdxQ==';
$redirectUrl = "http://".$_SERVER['HTTP_HOST']."/completed.php";
$failed_redirect_url = "http://".$_SERVER['HTTP_HOST']."/order_failed.php";
$json = file_get_contents('json/result.json');
$json_decoded = json_decode($json,true);

if ($json_decoded['merchantSecretKey'] != $secret_key) {
    echo "<script> window.location.replace('$failed_redirect_url');</script>";
    //die('Php Client - WRONG SECRET KEY');
}

if ($json_decoded['status'] == 0) {
    echo "<script> window.location.replace('$failed_redirect_url');</script>";
    //die('Php Client - ORDER WAS NOT COMPLETED');
} elseif ($json_decoded['status'] == 1) {

} else {
    echo "<script> window.location.replace('$failed_redirect_url');</script>";
    //die('Php Client - ORDER WAS CANCELLED');
}
foreach ($_SESSION["cart_products"] as $value) {
    unset($value);
}
echo "<script> window.location.replace('$redirectUrl');</script>";
