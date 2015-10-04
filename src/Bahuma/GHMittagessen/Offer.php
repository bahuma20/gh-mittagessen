<?php

/**
 * Created by PhpStorm.
 * User: BachhuberMax
 * Date: 01.10.2015
 * Time: 15:44
 */

namespace Bahuma\GHMittagessen;

use Bahuma\MiniORM\DataObject;

class Offer extends DataObject {
    /**
     * @var string
     */
    public static $tableName = 'offer';

    /**
     * @var array
     */
    public static $fields = array('id', 'user', 'restaurant', 'order_until');

    /**
     * @var int
     */
    private $user;

    /**
     * @var int
     */
    private $restaurant;

    /**
     * @var string
     */
    private $order_until;









    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param int $restaurant
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;
    }

    /**
     * @return Carbon
     */
    public function getOrderUntil() {
        return $this->order_until;
    }

    /**
     * @param string $order_until
     */
    public function setOrderUntil($order_until)
    {
        $this->order_until = $order_until;
    }








}