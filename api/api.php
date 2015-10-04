<?php

session_start();

use Bahuma\GHMittagessen\MailSubscription;
use Bahuma\GHMittagessen\MailSubscriptionNotFoundException;
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
    print_r($body);
    if (property_exists($body, 'image_url'))
        $restaurant->setImageUrl($body->image_url);

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
    $offer->setOrderUntil($body->order_until);
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

$app->post('/upload/image', function() use ($app) {
    // Setup file storage
    $file_storage = new \Upload\Storage\FileSystem("../assets/images");

    $file = new Upload\File("file", $file_storage);

    $file->addValidations(array(
        new Upload\Validation\Mimetype(array('image/png', 'image/gif', 'image/jpeg', 'video/JPEG', 'video/jpeg2000'))
    ));

    $file->setName(uniqid());

    try {
        // Success!
        $file->upload();
        header("Content-Type: text/html");
        print json_encode(array(
            "status" => "success",
            "filename" => $file->getName() . '.' . $file->getExtension()
        ));
        exit;
    } catch (\Exception $e) {
        header("Content-Type: text/html");
        print json_encode(array(
            "status" => "error",
            "errors" => $file->getErrors()
        ));
        exit;
    }
});

$app->post('/upload/file', function() use ($app) {
    // Setup file storage
    $file_storage = new \Upload\Storage\FileSystem("../assets/files");

    $file = new Upload\File("file", $file_storage);

    $file->addValidations(array(
        new Upload\Validation\Mimetype(array('application/pdf'))
    ));

    $file->setName(uniqid());

    try {
        // Success!
        $file->upload();
        header("Content-Type: text/html");
        print json_encode(array(
            "status" => "success",
            "filename" => $file->getName() . '.' . $file->getExtension()
        ));
        exit;
    } catch (\Exception $e) {
        header("Content-Type: text/html");
        print json_encode(array(
            "status" => "error",
            "errors" => $file->getErrors()
        ));
        exit;
    }
});

$app->get("/mailsubscription/:uid", function($uid) {
    try {
        $mailSubscription = MailSubscription::getByUserId($uid);
        $subscribed = true;
    } catch (MailSubscriptionNotFoundException $e) {
        $subscribed = false;
    }

    outputJSON(array("subscribed" => $subscribed));
});

$app->get("/mailsubscription/subscribe/:uid", function($uid) {
    try {
        $mailSubscription = MailSubscription::getByUserId($uid);
    } catch (MailSubscriptionNotFoundException $e) {
        $mailSubscription = new MailSubscription();
        $mailSubscription->setUser($uid);
        $mailSubscription->save();
    }
});

$app->get("/mailsubscription/unsubscribe/:uid", function($uid) {
    try {
        $mailSubscription = MailSubscription::getByUserId($uid);
        $mailSubscription->delete();
    } catch (MailSubscriptionNotFoundException $e) {

    }
});

$app->run();


