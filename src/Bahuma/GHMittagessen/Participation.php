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
     * @param $id
     * @return DataObject
     */
    public static function getByOfferId($offerId) {
        /**
         * @var $db \PDO
         */
        global $db;

        // Prepare SELECT
        $stmt = $db->prepare("SELECT id FROM ". self::$tableName ." WHERE offer = :offer_id");
        $stmt->bindParam(":offer_id", $offerId);
        $stmt->execute();

        $objectIds = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $objects = array();

        foreach ($objectIds as $objectId) {
            $objects[] = self::getById($objectId['id']);
        }

        return $objects;
    }




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