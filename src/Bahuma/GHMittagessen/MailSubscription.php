<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.10.15
 * Time: 22:32
 */

namespace Bahuma\GHMittagessen;


use Bahuma\MiniORM\DataObject;

class MailSubscription extends DataObject {

    /**
     * @var string
     */
    public static $tableName = "mailsubscription";

    /**
     * @var array
     */
    public static $fields = array('id', 'user');

    /**
     * @var int
     */
    private $user;

    public static function getByUserId($uid) {
        /**
         * @var $db \PDO
         */
        global $db;

        // Prepare SELECT
        $stmt = $db->prepare("SELECT `id` FROM " . self::$tableName . " WHERE user = ?");
        $stmt->execute(array($uid));

        if ($stmt->rowCount() == 0)
            throw new MailSubscriptionNotFoundException();

        $dataFromDB = $stmt->fetch(\PDO::FETCH_ASSOC);

        $mailSubscription = MailSubscription::getById($dataFromDB['id']);

        return $mailSubscription;
    }




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


}