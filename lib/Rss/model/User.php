<?php
namespace Rss\Model;

/**
 * Class User
 *
 * Class represents the user of the app. Contains an id and a uniqid.
 *
 * @package Rss\Model
 */
class User extends Model
{

    private $id;
    private $hash;

    public function __construct()
    {
        $this->hash = uniqid();
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
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }


} 