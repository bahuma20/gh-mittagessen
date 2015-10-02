<?php

use Bahuma\GHMittagessen\Offer;
use Bahuma\GHMittagessen\Participation;
use Bahuma\GHMittagessen\Restaurant;

require_once('../vendor/autoload.php');

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

$app->get('/restaurant', function() {
    outputJSON(Restaurant::getAll());
});

$app->get('/restaurant/:id', function($id){
   outputJSON(Restaurant::getById($id));
});

$app->post('/restaurant', function() use ($app) {
    $body = json_decode($app->request->getBody());

    $restaurant = new Restaurant();
    $restaurant->setName($body->name);
    $restaurant->setSpeisekartenUrl($body->speisekarten_url);
    $restaurant->save();

});

$app->get('/participation', function() {
    outputJSON(Participation::getAll());
});

$app->get('/participation/:id', function($id){
    outputJSON(Participation::getById($id));
});

$app->post('/participation', function() use ($app) {
    $body = json_decode($app->request->getBody());

    $participation = new Participation();
    $participation->setOffer($body->offer);
    $participation->setUser($body->user);
    $participation->setOrder($body->order);
    $participation->save();
});

$app->get('/offer', function() {
    outputJSON(Offer::getAll());
});

$app->get('/offer/:id', function($id){
    outputJSON(Offer::getById($id));
});

$app->get('/offer/:id/participation', function($id){
    outputJSON(Participation::getByOfferId($id));
});


$app->post('/offer', function() use ($app) {
    $body = json_decode($app->request->getBody());

    $offer = new Offer();
    $offer->setUser($body->user);
    $offer->setRestaurant($body->restaurant);
    $offer->setOrderUntil(new \Carbon\Carbon($body->order_until));
    $offer->save();
});

$app->run();


