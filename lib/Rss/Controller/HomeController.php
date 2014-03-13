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


        $public_feeds = $feed_repo->getAll();
        $user_feeds = $feed_repo->getAllByUser($user);
        $total_user_feeds = count($user_feeds);
        $total_public_feeds = count($public_feeds);

        $user_feeds = array_slice($user_feeds, 0, 10);
        $public_feeds = array_slice($public_feeds, 0, 10);


        return $this->twig->render('index.html.twig', array(
            'user_id' => $user->getId(),
        ));
    }



    public function indexPostAction()
    {
        return "Please enable javascript";
    }
}