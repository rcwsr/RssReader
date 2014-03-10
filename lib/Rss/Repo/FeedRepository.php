<?php
/**
 * Created by PhpStorm.
 * User: robincawser
 * Date: 09/03/2014
 * Time: 15:20
 */

namespace Rss\Repo;


use Rss\Exception\FeedIdNotFoundException;
use Rss\Exception\InvalidDBParameterException;
use Rss\Exception\UserIdNotFoundException;
use Rss\Model\Feed;
use Rss\Model\Model;
use Rss\Model\User;

class FeedRepository extends Repository
{

    /**
     * @param $data
     * @return int
     * @throws \Exception
     * @throws \PDOException
     * @throws \InvalidArgumentException
     * @throws \Rss\Exception\InvalidDBParameterException
     */
    public function insert(Model $feed)
    {
        if (!$feed instanceof Feed) {
            throw new \InvalidArgumentException("Must be an instance of Feed");
        }

        //check if user exists:
        $sql = $this->db->prepare('SELECT id FROM users WHERE id = :id LIMIT 1;');
        $sql->execute(array('id' => $feed->getUserId()));

        if(!$sql->fetch()){
            throw new UserIdNotFoundException(
                sprintf("A user with the ID %s does not exist", $feed->getUserId()));
        }


        //validate here
        $data = array(
            'url' => $feed->getUrl(),
            'title' => $feed->getTitle(),
            'user_id' => $feed->getUserId()
        );



        $sql = $this->db->prepare("INSERT INTO feeds (url, title, user_id) VALUES (:url, :title, :user_id)");
        $sql->execute($data);


        return (int)$this->db->lastInsertId();
    }

    public function delete($id)
    {
        if (!is_int($id)) {
            throw new \InvalidArgumentException("Must be an integer");
        }

        $sql = $this->db->prepare("DELETE FROM feeds WHERE id = ?");
        $sql->execute(array($id));

        if ($sql->rowCount() === 0) {
            throw new FeedIdNotFoundException();
        }

        return true;
    }

    public function getOne($id)
    {
        $sql = $this->db->prepare('SELECT id, title, url, user_id
                                      FROM feeds
                                      WHERE id = :id
                                      LIMIT 1;');


        $sql->execute(array('id' => $id));
        $sql->setFetchMode(\PDO::FETCH_CLASS, 'Rss\Model\Feed');
        $feed = $sql->fetch();

        return $feed;
    }

    /**
     * @param null $limit
     * @return array
     * @throws \Exception
     * @throws \PDOException
     * @throws \InvalidArgumentException
     */
    public function getAll($limit = null)
    {
        if ($limit) {
            if (!is_int($limit)) {
                throw new \InvalidArgumentException("Limit must be an int");
            }
            $results = $this->db->prepare('SELECT id, title, url, user_id
                                           From feeds
                                           ORDER BY date_added DESC
                                           LIMIT :limit;');
            $results->bindValue(':limit', $limit, \PDO::PARAM_INT);

        } else {
            $results = $this->db->prepare('SELECT id, title, url, user_id
                                           From feeds
                                           ORDER BY date_added DESC;');

        }
        try{
            $results->execute();
            $results->setFetchMode(\PDO::FETCH_CLASS, Feed::class);
            $feeds = $results->fetchAll();
        }catch (\PDOException $e) {
            throw $e;
        }


        return $feeds;
    }

    /**
     * @param User $user
     * @param null $limit
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Rss\Exception\UserIdNotFoundException
     */
    public function getAllByUser(User $user, $limit = null)
    {
        //check limit parameter is an int
        if ($limit) {
            if (!is_int($limit)) {
                throw new \InvalidArgumentException("Limit must be an int");
            }
        }

        //check if user exists:
        $sql = $this->db->prepare('SELECT id FROM users WHERE id = :id LIMIT 1;');
        $sql->execute(array('id' => $user->getId()));

        if(!$sql->fetch()){
            throw new UserIdNotFoundException(
                sprintf("A user with the ID %s does not exist", $user->getId()));
        }

        //perform queries
        if($limit){
            $results = $this->db->prepare('SELECT id, title, url, user_id
                                           FROM feeds
                                           WHERE user_id = :user_id
                                           ORDER BY date_added DESC
                                           LIMIT :limit;');
            $results->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $results->bindValue(':user_id', $user->getId(), \PDO::PARAM_INT);

        } else {
            $results = $this->db->prepare('SELECT id, title, url, user_id
                                           FROM feeds
                                           WHERE user_id = :user_id
                                           ORDER BY date_added DESC;');
            $results->bindValue(':user_id', $user->getId(), \PDO::PARAM_INT);
        }
        $results->execute();
        $results->setFetchMode(\PDO::FETCH_CLASS, Feed::class);
        $feeds = $results->fetchAll();

        return $feeds;
    }




}