<?php
/**
 * Created by PhpStorm.
 * User: robincawser
 * Date: 09/03/2014
 * Time: 15:20
 */

namespace Rss\Repo;


use Rss\Exception\InvalidDBParameterException;
use Rss\Exception\DuplicateUserHashException;

class UserRepository extends Repository
{

    public function insert($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException("Must be an array");
        }

        try {
            $sql = $this->db->prepare("INSERT INTO users (hash) VALUES (:hash);");
            $sql->execute($data);
        } catch (\PDOException $e) {
            switch ($e->getCode()) {
                case 'HY093':
                    throw new InvalidDBParameterException();
                    break;
                case '23000':
                    throw new DuplicateUserHashException();
                    break;
                default:
                    throw $e;
            }
        }
        return (int) $this->db->lastInsertId();
    }

    public function delete($id)
    {
        if(is_int($id)){
            $sql_string = "DELETE FROM users WHERE user_id = ?;";
        }elseif(is_string($id)){
            $sql_string = "DELETE FROM users WHERE hash = ?;";
        }
        else{
            throw new \InvalidArgumentException("id param must be int or string");
        }

        $sql = $this->db->prepare($sql_string);
        $sql->execute(array($id));

        return true;
    }

} 