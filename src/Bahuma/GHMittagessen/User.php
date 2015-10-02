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

    /**
     * @param $username string
     * @return User
     * @throws UserNotFoundException
     */
    public static function getByUsername($username) {
        /**
         * @var $userdb \PDO
         */
        global $userdb;

        // Prepare SELECT
        $stmt = $userdb->prepare("SELECT `id` FROM jos_users WHERE username = ?");
        $stmt->execute(array($username));

        if ($stmt->rowCount() == 0)
            throw new UserNotFoundException();

        $dataFromDB = $stmt->fetch(\PDO::FETCH_ASSOC);

        $user = User::getById($dataFromDB['id']);

        return $user;
    }

    /**
     * @param $password string
     * @throws PasswordIncorrectException
     */
    public function login($password) {
        // Split the password hash in hash and salt
        list($hash,$salt) = explode(':', $this->getPasswordHash());

        // generate salted hash for user submitted password
        $crypto = md5($password.$salt);

        // check if successfull
        if ($crypto==$hash) {
            $_SESSION['userID'] = $this->getId();
            return true;
        } else {
            throw new PasswordIncorrectException();
        }

    }

    public static function logout() {
        unset($_SESSION['userID']);
    }

    public static function getLoggedInUser() {
        if (!array_key_exists('userID', $_SESSION))
            return false;

        return User::getById($_SESSION['userID']);
    }

    function jsonSerialize() {
        $result = array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail()
        );

        return $result;
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