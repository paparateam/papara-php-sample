<?php
$data = file_get_contents(('php://input'), true);
$secret_key = '0MkOLqXP0qxzse19AHF1NRPM6LP23lO1QfTGSpW7y/CBanDbRS7LqDMVHMuzMM6J45H6eqhndNJTuUN5oZQdxQ==';
$notification_file = fopen("json/result.json", "w");

fwrite($notification_file,$data);

if (file_exists('json/all_ipn.json') == false) {
    $all_ipns_file = fopen("json/all_ipn.json","w");
    fwrite($all_ipns_file,'['.$data.']');
    fclose($all_ipns_file);
} else {
    $all_ipns_file = fopen("json/all_ipn.json","r+");
    fseek($all_ipns_file,-1,SEEK_END);
    fwrite($all_ipns_file,",".$data."]");
    fclose($all_ipns_file);
}