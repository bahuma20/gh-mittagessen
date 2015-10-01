<?php

use Bahuma\GHMittagessen\Offer;
use Bahuma\GHMittagessen\Participation;
use Bahuma\GHMittagessen\Restaurant;

require_once('vendor/autoload.php');

$db = new PDO('mysql:dbname=mittagesser;host=localhost', 'test', 'testpw');


function outputJSON($data) {
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}


$app = new \Slim\Slim();

$app->get('/test', function () {
    echo "It is running!!!";
});

$app->get('/restaurants', function() {
    outputJSON(Restaurant::getAll());
});

$app->get('/participations', function() {
    outputJSON(Participation::getAll());
});

$app->get('/offers', function() {
    outputJSON(Offer::getAll());
});

$app->run();


