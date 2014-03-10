<?php
/**
 * Created by PhpStorm.
 * User: robincawser
 * Date: 09/03/2014
 * Time: 15:20
 */

namespace Rss\Repo;


use Rss\Exception\DuplicateUserHashException;
use Rss\Exception\UserIdNotFoundException;
use Rss\Model\Model;
use Rss\Model\User;

class UserRepository extends Repository
{

    /**
     * @param Model $user
     * @return int
     * @throws \Rss\Exception\DuplicateUserHashException
     * @throws \Exception
     * @throws \PDOException
     * @throws \InvalidArgumentException
     * @throws \Rss\Exception\InvalidDBParameterException
     */
    public function insert(Model $user)
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException("Must be an instance of User");
        }

        try {
            if ($this->getOne($user->getHash()) instanceof User) {
                throw new DuplicateUserHashException();
            }
        } catch (UserIdNotFoundException $e) {

        }


        //validate here

        $data = array(
            'hash' => $user->getHash(),
        );


        try {
            $sql = $this->db->prepare("INSERT INTO users (hash) VALUES (:hash);");
            $sql->execute($data);
        } catch (\PDOException $e) {
            throw $e;

        }
        return (int)$this->db->lastInsertId();
    }

    public function getOne($id)
    {
        if (is_int($id)) {
            $sql_string = "SELECT id, hash FROM users WHERE id = ? LIMIT 1;";
        } elseif (is_string($id)) {
            $sql_string = "SELECT id, hash FROM users WHERE hash = ? LIMIT 1;";
        } else {
            throw new \InvalidArgumentException("id param must be int or string");
        }

        $sql = $this->db->prepare($sql_string);


        $sql->execute(array($id));
        $sql->setFetchMode(\PDO::FETCH_CLASS, 'Rss\Model\User');
        $user = $sql->fetch();

        if (!$user) {
            throw new UserIdNotFoundException();
        }
        return $user;
    }

    public function delete($id)
    {
        if (is_int($id)) {
            $sql_string = "DELETE FROM users WHERE id = ?;";
        } elseif (is_string($id)) {
            $sql_string = "DELETE FROM users WHERE hash = ?;";
        } else {
            throw new \InvalidArgumentException("id param must be int or string");
        }

        $sql = $this->db->prepare($sql_string);
        $sql->execute(array($id));

        if ($sql->rowCount() === 0) {
            throw new UserIdNotFoundException();
        }

        return true;

    }

    public function getAll($limit = null)
    {
        // TODO: Implement getAll() method.
    }


} 