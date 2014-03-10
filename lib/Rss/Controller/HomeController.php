<?php

namespace Rss\Controller;

use Rss\Crypto;
use Rss\Exception\DuplicateUserHashException;
use Rss\Exception\UserIdNotFoundException;
use Rss\Model\Feed;
use Rss\Model\User;
use Rss\Provider\UserProvider;
use Rss\Repo\FeedRepository;
use Rss\Repo\UserRepository;

class HomeController extends Controller
{


    /**
     * @return string
     */
    public function indexGetAction()
    {

        //Get user from cookie
        $user_provider = new UserProvider($this->config);
        $user = $user_provider->getUser();

        $feed_repo = new FeedRepository($this->config);

        //Get public and user feeds

        $user_feeds = $feed_repo->getAllByUser($user);
        $public_feeds = $feed_repo->getAll(20);




        return $this->twig->render('index.html.twig', array(
            'public_feeds' => $public_feeds,
            'user_feeds' => $user_feeds,
        ));
    }



    public function indexPostAction()
    {
        return "Please enable javascript";
    }
}