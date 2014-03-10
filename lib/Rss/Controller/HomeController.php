<?php

namespace Rss\Controller;

use Rss\Crypto;
use Rss\Exception\DuplicateUserHashException;
use Rss\Exception\UserIdNotFoundException;
use Rss\Model\User;
use Rss\Repo\FeedRepository;
use Rss\Repo\UserRepository;

class HomeController extends Controller
{
    private $hash_secret = ";WvfP2[,BUC{6gY";

    /**
     * @return string
     */
    public function indexGetAction()
    {
        //Check is user cookie exists
        if (isset($_COOKIE['rss_user_hash'])) {
            //TODO sanitise cookie here

            //decrypt cookie hash
            $crypto = new Crypto($this->hash_secret);
            $hash = $crypto->decrypt($_COOKIE['rss_user_hash']);

            //find user
            try {
                $user_repo = new UserRepository($this->config);
                $user = $user_repo->getOne($hash);
            } catch (UserIdNotFoundException $e) {
                //If user isn't found, create a new user
                $user = $this->createUser();
            }
        } else {
            //If user cookie does not exist, create a new user and create their cookie
            $user = $this->createUser();
        }


        $feed_repo = new FeedRepository($this->config);

        //Get public and user feeds

        $user_feeds = $feed_repo->getAllByUser($user);
        $public_feeds = $feed_repo->getAll(20);


        for($i = 0; $i < 10; $i++)

        return $this->twig->render('index.html.twig', array(
            'public_feeds' => $public_feeds,
            'user_feeds' => $user_feeds,
        ));
    }

    /**
     * @return User
     */
    private function createUser()
    {
        $user_repo = new UserRepository($this->config);
        $user = new User();
        $crypto = new Crypto($this->hash_secret);
        try {
            //Insert user
            $user_id = $user_repo->insert($user);
            $user->setId($user_id);
        } catch (DuplicateUserHashException $e) {
            //If user hash exists (although unlikely), try again
            $this->createUser();
        }

        //create cookie for 10 years with encrypted user hash stored in it
        setcookie("rss_user_hash", $crypto->encrypt($user->getHash()),
            time() + (10 * 365 * 24 * 60 * 60)
        );
        return $user;
    }

    public function indexPostAction()
    {
        return "hello";
    }
}