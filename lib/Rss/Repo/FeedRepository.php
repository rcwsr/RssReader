<?php
/**
 * Created by PhpStorm.
 * User: robincawser
 * Date: 09/03/2014
 * Time: 15:20
 */

namespace Rss\Repo;


use Rss\Exception\InvalidDBParameterException;

class FeedRepository extends Repository
{

    public function insert($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException("Must be an array");
        }

        try {
            $sql = $this->db->prepare("INSERT INTO feeds (url, title) VALUES (:url, :title)");
            $sql->execute($data);
        } catch (\PDOException $e) {
            switch ($e->getCode()) {
                case 'HY093':
                    throw new InvalidDBParameterException();
                    break;
                default:
                    throw $e;
            }
        }
        return (int) $this->db->lastInsertId();
    }

    public function delete($id)
    {
        if (!is_int($id)) {
            throw new \InvalidArgumentException("Must be an integer");
        }

        $sql = $this->db->prepare("DELETE FROM feeds WHERE feed_id = ?");
        $sql->execute(array($id));

        return true;
    }

} 