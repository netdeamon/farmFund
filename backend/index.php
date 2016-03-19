<?php
//require '../vendor/autoload.php';
//$app->response()->header('Access-Control-Allow-Origin','*');
require '../vendor/slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
'mode' => 'development'
));

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

 // $username = "discu6zu_santosh";
 // $password = "aa1!aa1!";
 $username = "root";
 $password = "";
// $conn = new PDO ( 'mysql:host=localhost;dbname=discu6zu_discuss-book', $username, $password );

 $conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
//
$app->get ( "/", function () use($app) {
  echo 'API VERSION 1.0';
});
$app->run();
?>
