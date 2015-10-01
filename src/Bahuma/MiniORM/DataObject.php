<?php
namespace Bahuma\MiniORM;

abstract class DataObject implements \JsonSerializable {
    /**
     * @var string
     */
    public static $tableName;

    /**
     * @var array
     */
    private static $fields = array('id');

    /**
     * @var bool
     */
    private $isNew = true;

    /**
     * @var int
     */
    private $id;


    function __construct()
    {

    }

    /**
     * @param $id
     * @return DataObject
     */
    public static function getById($id) {
        /**
         * @var $db \PDO
         */
        global $db;

        // Get static properties from sub class
        $classname = get_called_class();
        $classVars = get_class_vars($classname);

        // Prepare SELECT
        $stmt = $db->prepare("SELECT * FROM ". $classVars['tableName'] ." WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);


        // Create new Instance
        /**
         * @var $object DataObject
         */
        $object = new $classname;

        // Declare Instance as not new
        $object->setIsNew(false);

        // Set fields of the Instance
        foreach ($result as $field_name => $value) {
            $setter = "set" . str_replace(' ', '', ucwords(str_replace('_', ' ', $field_name)));
            $object->$setter($result[$field_name]);
        }

        // Return object
        return $object;
    }


    public static function getAll() {
        /**
         * @var $db \PDO
         */
        global $db;

        // Get static properties from sub class
        $classname = get_called_class();
        $classVars = get_class_vars($classname);

        // Prepare SELECT
        $stmt = $db->prepare("SELECT id FROM ". $classVars['tableName']);
        $stmt->execute();

        $objectIds = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $objects = array();

        foreach ($objectIds as $objectId) {
            $objects[] = $classname::getById($objectId['id']);
        }

        return $objects;
    }

    /**
     * Saves an Object to the Database
     * @return DataObject
     */
    public function save() {
        if (!$this->isNew) {
            return false;
        }

        /**
         * @var $db \PDO
         */
        global $db;

        // Get static properties from sub class
        $classname = get_called_class();
        $classVars = get_class_vars($classname);

        // Filter id from field list
        $mFields = $classVars['fields'];
        if(($key = array_search('id', $mFields)) !== false) {
            unset($mFields[$key]);
        }

        // Prepare query
        $query = 'INSERT INTO '. $classVars['tableName'] . '(';

        // Add fields to query
        $i = 0;
        foreach ($mFields as $field) {
            $query .= '`'. $field . '`';

            if (++$i !== count($mFields))
                $query .= ', ';
        }

        $query .= ') VALUES (';

        // Add value placeholders to query
        for($i=0; $i<count($mFields); $i++) {
            $query .= '?';

            if ($i < count($mFields) - 1)
                $query .= ', ';
        }

        $query .= ')';


        // Prepare query
        $stmt = $db->prepare($query);

        // Get values from Object
        $values = array();
        $i = 0;
        foreach ($mFields as $field_name) {
            $i++;

            $getter = "get" . str_replace(' ', '', ucwords(str_replace('_', ' ', $field_name)));
            $values[] = $this->$getter();
        }

        // Write to database
        $stmt->execute($values);

        // Set id to the DataOject
        $this->setId($db->lastInsertId());

        // Declare this DataObject as not new
        $this->setIsNew(false);

        return $this;
    }

    function jsonSerialize()
    {
        // Get static properties from sub class
        $classname = get_called_class();
        $classVars = get_class_vars($classname);

        $result = array();

        foreach($classVars['fields'] as $field_name) {
            $getter = "get" . str_replace(' ', '', ucwords(str_replace('_', ' ', $field_name)));
            $result[$field_name] = $this->$getter();
        }

        return $result;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return boolean
     */
    public function isIsNew()
    {
        return $this->isNew;
    }

    /**
     * @param boolean $isNew
     */
    public function setIsNew($isNew)
    {
        $this->isNew = $isNew;
    }

}