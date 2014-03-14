<?php

namespace Rss\Repo;


use Rss\Database;
use Rss\Model\Model;

/**
 * Class Repository
 *
 * Abstract repository class. Upon construction loads the database PDO object so that extending Repo classes don't
 * need to. Provides abstract methods that other repos must implement. Upon destruction of object, PDO is nullified.
 *
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

    public abstract function insert(Model $model);
    public abstract function delete($id);
    public abstract function getOne($id);
    public abstract function getAll($limit);
    public function __destruct()
    {
        $db = null;
    }
} 