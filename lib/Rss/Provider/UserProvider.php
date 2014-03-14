<?php

namespace Rss\Provider;


use Rss\Crypto;
use Rss\Exception\DuplicateUserHashException;
use Rss\Exception\UserIdNotFoundException;
use Rss\Model\User;
use Rss\Repo\UserRepository;

/**
 * Class UserProvider
 *
 * This class provides the current user of the system. If no user is defined in the cookie, a new one will be created
 * and added to the database.
 *
 * @package Rss\Provider
 */
class UserProvider
{
    private $crypto;
    private $config;

    /**
     * Creates encryption class and user repo upon instantiation
     *
     * @param $config Requires the config file in order to use Repository classes
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->crypto = new Crypto($config['crypto']['secret']);
        $this->user_repo = new UserRepository($this->config);
    }

    /**
     * Get's the user of the system, whether an already existing user or a newly created user
     * @return mixed|User
     */
    public function getUser()
    {
        if (isset($_COOKIE['rss_user_hash'])) {
            //TODO sanitise cookie here

            //decrypt cookie hash
            //TODO move cookie name to a constant
            $hash = $this->crypto->decrypt($_COOKIE['rss_user_hash']);

            //find user
            try {
                $user_repo = new UserRepository($this->config);
                return $user_repo->getOne($hash);
            } catch (UserIdNotFoundException $e) {
                //If user isn't found, create a new user
                return $this->createUser();
            }
        } else {
            //If user cookie does not exist, create a new user and create their cookie
            return $this->createUser();
        }
    }

    /**
     * Creates a new user. If user already exists, it tries again!
     * @return User
     */
    private function createUser()
    {
        $user = new User();

        try {
            //Insert user
            $user_id = $this->user_repo->insert($user);
            $user->setId($user_id);
        } catch (DuplicateUserHashException $e) {
            //If user hash exists (although unlikely), try again
            $this->createUser();
        }

        //create cookie for 10 years with encrypted user hash stored in it
        setcookie("rss_user_hash", $this->crypto->encrypt($user->getHash()),
            time() + (10 * 365 * 24 * 60 * 60)
        );
        return $user;
    }
} 