<?php

namespace Rss\Controller;

use Rss\Exception\UserIdNotFoundException;
use Rss\Model\Feed;
use Rss\Provider\UserProvider;
use Rss\Repo\FeedRepository;
use Rss\Validator\RssValidator;

class AjaxController extends Controller
{
    /**
     * @return string
     */
    public function addFeedAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['url'])) {

                $url = $_POST['url'];

                if (RssValidator::validateUrl($url)) {
                    $rss = RssValidator::validateDoc($url);
                    if ($rss) {

                        //Get current user
                        $user_provider = new UserProvider($this->config);
                        $user = $user_provider->getUser();

                        $title = $rss->getElementsByTagName('title')->item(0)->nodeValue;
                        $feed = new Feed();
                        $feed->setTitle($title)
                            ->setUrl($url)
                            ->setUserId($user->getId());

                        $feed_repo = new FeedRepository($this->config);
                        try {
                            $feed_repo->insert($feed);
                            return json_encode(array('success' => "Feed added successfully"));
                        } catch (UserIdNotFoundException $e) {
                            return json_encode(array('error' => $e->getMessage()));
                        }
                    } else {
                        return json_encode(array('error' => "Invalid RSS feed"));
                    }

                } else {
                    return json_encode(array('error' => "Invalid URL"));
                }
            }
        } else {
            throw new \Exception();
        }
    }


    /**
     * @return string
     */
    public function loadMyFeedsInclude($limit = null)
    {

        $user_provider = new UserProvider($this->config);
        $user = $user_provider->getUser();

        $feed_repo = new FeedRepository($this->config);


        $user_feeds = $feed_repo->getAllByUser($user, $limit);

        return $this->twig->render('myfeeds.html.twig', array(
            'user_feeds' => $user_feeds,
        ));
    }

    public function loadPublicFeedsInclude($limit = null)
    {

        $feed_repo = new FeedRepository($this->config);

        $public_feeds = $feed_repo->getAll($limit);

        return $this->twig->render('publicfeeds.html.twig', array(
            'public_feeds' => $public_feeds,
        ));
    }

    public function getFeedAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['feed_id'])) {

                //TODO santise feed id
                $feed_id =  $_POST['feed_id'];
                $feed_repo = new FeedRepository($this->config);
                $feed = $feed_repo->getOne($feed_id);
                return $this->twig->render('rssview.html.twig', array('feed' => $feed));
            }
        }

    }


}