<?php

namespace Rss\Controller;

use Rss\Crypto;
use Rss\Exception\DuplicateUserHashException;
use Rss\Exception\UserIdNotFoundException;
use Rss\Model\Feed;
use Rss\Model\User;
use Rss\Repo\FeedRepository;
use Rss\Repo\UserRepository;

class AjaxController extends Controller
{
    private $hash_secret = ";WvfP2[,BUC{6gY";

    /**
     * @return string
     */
    public function addFeedAction()
    {

    }
}