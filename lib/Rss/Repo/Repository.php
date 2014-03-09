<?php
/**
 * Created by PhpStorm.
 * User: robincawser
 * Date: 09/03/2014
 * Time: 15:13
 */

namespace Rss\Repo;

use Rss\Database;

/**
 * Class Repository
 * @package Rss\Repo
 */
abstract class Repository
{
    protected $config;
    protected $db;

    public function __construct($config)
    {
        $this->config = $config;
        $this->db = Database::connect($config['db']);
    }

    public abstract function insert($data);
    public abstract function delete($id);

    public function __destruct()
    {
        $db = null;
    }
} 