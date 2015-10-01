<?php

require_once('vendor/autoload.php');

use Bahuma\GHMittagessen\Offer;
use Bahuma\GHMittagessen\Restaurant;
use Carbon\Carbon;

$db = new PDO('mysql:dbname=mittagesser;host=localhost', 'root', '');

$rest = Restaurant::findById(1);


$rest2 = new Restaurant();
$rest2->setName('Restaurant 3');
$rest2->setSpeisekartenUrl('http://yoloswag');
//$rest2->save();

$offer = new Offer();
$offer->setUser(1);
$offer->setRestaurant(2);
$offer->setOrderUntil(new Carbon());
//$offer->save();

print '<html><pre>';
print_r($rest2->getId());


