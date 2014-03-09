<?php

namespace Rss;

use Rss\Exception\DatabaseException;

/**
 * Class Database
 * @package Rss
 */
class Database
{

    public static function connect($db_config)
    {
        if (!is_array($db_config)) {
            throw new \InvalidArgumentException("Must be an array");
        }

        $host = $db_config['host'];
        $user = $db_config['user'];
        $password = $db_config['password'];
        $db = $db_config['name'];
        $port = $db_config['port'];


        $pdo = new \PDO("mysql:host=$host;port=$port;dbname=$db", $user, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);


        return $pdo;
    }


} 