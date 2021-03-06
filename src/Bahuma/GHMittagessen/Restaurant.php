<?php
/**
 * Created by PhpStorm.
 * User: BachhuberMax
 * Date: 01.10.2015
 * Time: 10:45
 */
namespace Bahuma\GHMittagessen;

use Bahuma\MiniORM\DataObject;

class Restaurant extends DataObject {
    /**
     * @var string
     */
    public static $tableName = 'restaurant';

    /**
     * @var array
     */
    public static $fields = array('id', 'name', 'speisekarten_url', 'image_url');

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $speisekarten_url;

    /**
     * @var string
     */
    private $image_url = "restaurant_placeholder.png";




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
    public function getSpeisekartenUrl()
    {
        return $this->speisekarten_url;
    }

    /**
     * @param mixed $speisekarten_url
     */
    public function setSpeisekartenUrl($speisekarten_url)
    {
        $this->speisekarten_url = $speisekarten_url;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * @param string $image_url
     */
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
    }


}