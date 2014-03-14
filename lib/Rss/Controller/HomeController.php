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

/**
 * Class HomeController
 *
 * Handles loading the homepage of the system. Simply loads the index twig template which in turn loads feeds through
 * ajax calls.
 *
 * @package Rss\Controller
 */
class HomeController extends Controller
{


    /**
     * Loads the current user when app homepage is loaded. returns index.html.twig template
     *
     * @return string twig template
     */
    public function indexGetAction()
    {

        //Get user from cookie
        $user_provider = new UserProvider($this->config);
        $user = $user_provider->getUser();



        return $this->twig->render('index.html.twig', array(
            'user_id' => $user->getId(),
        ));
    }


    /**
     * If the index is accessed via POST, an error message is displayed asking the user to enable JS.
     *
     * @return string error message
     */
    public function indexPostAction()
    {
        return "Please enable javascript";
    }
}