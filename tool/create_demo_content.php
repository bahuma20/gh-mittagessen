<?php
$db = new PDO('mysql:dbname=mittagesser;host=localhost', 'test', 'testpw');

use Bahuma\GHMittagessen\Offer;
use Bahuma\GHMittagessen\Participation;
use Bahuma\GHMittagessen\Restaurant;
use Carbon\Carbon;

require_once('../vendor/autoload.php');

// Restaurants

$restaurant1 = new Restaurant();
$restaurant1->setName("Bella Italia");
$restaurant1->setSpeisekartenUrl("http://test.de");
$restaurant1->save();

print "Created Restaurant 1 with id " . $restaurant1->getId();


$restaurant2 = new Restaurant();
$restaurant2->setName("Burgerladen");
$restaurant2->setSpeisekartenUrl("http://test2.de");
$restaurant2->save();

print "Created Restaurant 2 with id " . $restaurant2->getId();


// Offers
$offer1 = new Offer();
$offer1->setRestaurant($restaurant1->getId());
$offer1->setUser(1);
$offer1->setOrderUntil(new Carbon());
$offer1->save();

print "Created Offer 1 at Restaurant 1 with id " . $offer1->getId();


$offer2 = new Offer();
$offer2->setRestaurant($restaurant2->getId());
$offer2->setUser(1);
$offer2->setOrderUntil(new Carbon());
$offer2->save();

print "Created Offer 2 at Restaurant 2 with id " . $offer2->getId();


// Participation
$participation = new Participation();
$participation->setUser(2);
$participation->setOffer($offer1->getId());
$participation->setOrder("Einmal 2 halbe Hahn.");
$participation->save();

print "Created Participation at Offer 1 with id " . $participation->getId();