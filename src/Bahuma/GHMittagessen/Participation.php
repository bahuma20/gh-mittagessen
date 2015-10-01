<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 01.10.15
 * Time: 20:50
 */

namespace Bahuma\GHMittagessen;


use Bahuma\MiniORM\DataObject;

class Participation extends DataObject{
    /**
     * @var string
     */
    public static $tableName = 'participation';

    /**
     * @var array
     */
    public static $fields = array('id', 'user', 'offer', 'order');

    /**
     * @var int
     */
    private $user;

    /**
     * @var int
     */
    private $offer;

    /**
     * @var string
     */
    private $order;






    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param mixed $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }
}