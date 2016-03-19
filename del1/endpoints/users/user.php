<?php
$app->get ( "/getUserNames", function () use($app) {
$users=array();
$users[0]="John";
$users[1]="Stephen";
$users[2]="Mary";
$users[3]="Lorie";
$users[4]="Sonia";

$app->response ()->header ( 'Content-Type', 'application/json' );
$app->response ()->header ( 'access-control-allow-origin', 'http://localhost:8011' );
echo json_encode ( $users );


});
?>