<?php

session_start();

use Bahuma\GHMittagessen\Offer;
use Bahuma\GHMittagessen\Participation;
use Bahuma\GHMittagessen\PasswordIncorrectException;
use Bahuma\GHMittagessen\Restaurant;
use Bahuma\GHMittagessen\User;
use Bahuma\GHMittagessen\UserNotFoundException;
use Slim\Slim;

require_once('../vendor/autoload.php');

// Create connection to the database
$db = new PDO('mysql:dbname=mittagesser;host=localhost', 'test', 'testpw');

// Create connection to the joomla intranet to add the ability to login via intranet account
$userdb = new PDO('mysql:dbname=intranet;host=localhost', 'test', 'testpw');

/**
 * Send data to the user in JSON Format
 *
 * @param $data mixed The data which schould be send to the user
 */
function outputJSON($data) {
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}

function outputError($message, $code) {
    header("Content-Type: application/json");

    $response = new stdClass();
    $response->status = "error";
    $response->code = $code;
    $response->message = $message;
    echo json_encode($response);
    exit;
}


$app = new Slim();

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


$app->get('/user/:id', function($id) {
    outputJSON(User::getById($id));
});

$app->post('/auth/login', function() use ($app) {
    $body = json_decode($app->request->getBody());

    try {
        $user = User::getByUsername($body->username);

        try {
            $user->login($body->password);

            if ($user) {
                outputJSON(array(
                    'status' => 'success',
                    'message' => 'User logged in'
                ));
            }

        } catch (PasswordIncorrectException $e) {
            outputError($e->getMessage(), $e->getCode());
        }
    } catch (UserNotFoundException $e) {
        outputError($e->getMessage(), $e->getCode());
    }
});

$app->get('/auth/current', function() {
    $user = User::getLoggedInUser();
//    print_r($user);

    if ($user)
        outputJSON($user);
    else
        outputError('Not logged in', 100);
});

$app->get('/auth/logout', function() {
    User::logout();

    outputJSON(array('status'=>'success'));
});

$app->run();


