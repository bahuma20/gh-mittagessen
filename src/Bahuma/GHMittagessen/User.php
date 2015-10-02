<?php
/**
 * Created by PhpStorm.
 * User: BachhuberMax
 * Date: 02.10.2015
 * Time: 16:22
 */

namespace Bahuma\GHMittagessen;


use Bahuma\MiniORM\DataObject;

class User extends DataObject {
    private $name;
    private $username;
    private $email;
    private $passwordHash;


    public static function getById($id) {
        /**
         * @var $userdb \PDO
         */
        global $userdb;
        global $config;

        $stmt = $userdb->prepare("SELECT `id`, `name`, `username`, `email`, `password` FROM jos_users WHERE `id` = :user_id");
        $stmt->bindParam(':user_id', $id);
        $stmt->execute();

        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        $user = new self();
        $user->setIsNew(false);
        $user->setId($userData['id']);
        $user->setName($userData['name']);
        $user->setUsername($userData['username']);
        $user->setEmail($userData['email']);
        $user->setPasswordHash($userData['password']);

        return $user;
    }

    public function save() {
        return false;
    }

    public static function getAll() {
        return false;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * @param mixed $passwordHash
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }
}