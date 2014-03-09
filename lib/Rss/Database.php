<?php

namespace Rss;

/**
 * Class Database
 * @package Rss
 */
class Database
{
    private $pdo;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    }
} 