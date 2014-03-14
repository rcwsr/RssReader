<?php

namespace Rss;

use Rss\Exception\DatabaseException;

/**
 * Class Database
 * @package Rss
 */
class Database
{
    /**
     * Database connection method. Using PDO allows Repository classes to use prepared SQL statements.
     *
     * @param $db_config array of db config values
     * @return \PDO PDO database object
     * @throws \InvalidArgumentException
     */
    public static function connect($db_config)
    {
        //Check the config file is an array
        if (!is_array($db_config)) {
            throw new \InvalidArgumentException("Must be an array");
        }

        //Get config options from config file
        $host = $db_config['host'];
        $user = $db_config['user'];
        $password = $db_config['password'];
        $db = $db_config['name'];
        $port = $db_config['port'];

        //create PDO object
        $pdo = new \PDO("mysql:host=$host;port=$port;dbname=$db", $user, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);


        return $pdo;
    }


} 