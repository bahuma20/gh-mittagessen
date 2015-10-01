<?php

require_once('vendor/autoload.php');

$db = new PDO('mysql:dbname=mittagesser;host=localhost', 'test', 'testpw');


$app = new \Slim\Slim();

$app->get('/test', function () {
    echo "It is running!!!";
});

$app->run();


