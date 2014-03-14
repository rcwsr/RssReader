<?php

namespace Rss\Repo;


use Rss\Exception\FeedIdNotFoundException;
use Rss\Exception\UserIdNotFoundException;
use Rss\Model\Feed;
use Rss\Model\Model;
use Rss\Model\User;

/**
 * Class FeedRepository
 *
 * Repository class for RSS feeds. Allows insertion, retrieval and deletion of feeds from DB.
 * @package Rss\Repo
 */
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

        if (!$sql->fetch()) {
            throw new UserIdNotFoundException(sprintf("A user with the ID %s does not exist", $feed->getUserId()));
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

    /**
     * @param $id
     * @return bool
     * @throws \Rss\Exception\FeedIdNotFoundException
     * @throws \InvalidArgumentException
     */
    public function delete($id)
    {
        if (!is_int($id)) {
            throw new \InvalidArgumentException("Must be an integer");
        }

        $sql = $this->db->prepare("DELETE FROM feeds WHERE id = ?");
        $sql->execute(array($id));

        if ($sql->rowCount() === 0) {
            throw new FeedIdNotFoundException(sprintf("Feed with id %s could not be found", $id));
        }

        return true;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Rss\Exception\FeedIdNotFoundException
     * @throws \InvalidArgumentException
     */
    public function getOne($id)
    {
        if(!is_int($id)){
            throw new \InvalidArgumentException("Must be an integer");
        }
        $sql = $this->db->prepare('SELECT id, title, url, user_id FROM feeds WHERE id = :id LIMIT 1;');
        $sql->execute(array('id' => $id));
        $sql->setFetchMode(\PDO::FETCH_CLASS, Feed::class);
        $feed = $sql->fetch();


        if ($sql->rowCount() === 0) {
            throw new FeedIdNotFoundException(sprintf("Feed with id %s could not be found", $id));
        }

        return $feed;
    }

    /**
     * @param User $user
     * @return array
     * @throws \Rss\Exception\UserIdNotFoundException
     */
    public function getAllByUser(User $user, $limit = null)
    {

        if($limit && !is_int($limit)){
            throw new \InvalidArgumentException("Must be an integer");
        }

        //check if user exists:
        $sql = $this->db->prepare('SELECT id FROM users WHERE id = :id LIMIT 1;');
        $sql->execute(array('id' => $user->getId()));

        //If user does not exist, throw exception
        if (!$sql->fetch()) {
            throw new UserIdNotFoundException(sprintf("A user with the ID %s does not exist", $user->getId()));
        }

        $results = $this->db->prepare('SELECT id, title, url, user_id FROM feeds WHERE user_id = ? ORDER BY date_added DESC;');

        $results->execute(array($user->getId()));
        $results->setFetchMode(\PDO::FETCH_CLASS, Feed::class);
        $feeds = $results->fetchAll();

        if($limit){
            $feeds = array_slice($feeds, 0, $limit);
        }
        return $feeds;
    }

    /**
     * @param null $limit
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getAll($limit = null)
    {
        if($limit && !is_int($limit)){
            throw new \InvalidArgumentException("Must be an integer");
        }
        $results = $this->db->prepare('SELECT id, title, url, user_id FROM feeds ORDER BY date_added DESC;');
        $results->execute();

        $results->execute();
        $results->setFetchMode(\PDO::FETCH_CLASS, Feed::class);
        $feeds = $results->fetchAll();

        if($limit){
            $feeds = array_slice($feeds, 0, $limit);
        }
        return $feeds;
    }


}