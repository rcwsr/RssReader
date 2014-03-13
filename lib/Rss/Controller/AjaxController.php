<?php

namespace Rss\Controller;

use Rss\Exception\FeedIdNotFoundException;
use Rss\Exception\UserIdNotFoundException;
use Rss\Model\Feed;
use Rss\Model\Item;
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
                    try {
                        $rss = RssValidator::validateDoc($url);
                    } catch (\Exception $e) {

                    }


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

    public function deleteFeedAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['feed_id'])) {

                $feed_id = $_POST['feed_id'];


                if (is_numeric($feed_id)) {
                    $user_provider = new UserProvider($this->config);
                    $user = $user_provider->getUser();

                    $feed_repo = new FeedRepository($this->config);

                    try {
                        $feed = $feed_repo->getOne((int)$feed_id);
                    } catch (FeedIdNotFoundException $e) {
                        return json_encode(array('error' => $e->getMessage()));
                    }


                    if ($user->getId() === $feed->getUserId()) {
                        $feed_repo->delete($feed->getId());
                    } else {
                        return json_encode(array('error' => "You can't delete someone else's feed!"));
                    }
                    return json_encode(array('success' => "Feed deleted"));
                }
            }
        }
    }

    /**
     * Displays a
     */
    public function deleteFeedConfirmationAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['feed_id'])) {
                $feed_id = $_POST['feed_id'];

                return $this->twig->render('deletefeed.html.twig', array('feed_id' => $feed_id));
            }
        }

    }

    /**
     * @return string
     */
    public function loadUserFeedsIncludeAction($limit = 'all')
    {
        try {
            return $this->loadInclude($limit, 'user');
        } catch (\InvalidArgumentException $e) {
            return json_encode(array('error' => $e->getMessage()));
        }
    }

    private function loadInclude($limit, $feed_type)
    {
        //ensure $limit parameter is either numeric, or is 'all'
        if (!is_numeric($limit) && $limit !== 'all') {
            return json_encode(array('error' => "Limit must be an int"));
        }

        $user_provider = new UserProvider($this->config);
        $user = $user_provider->getUser();

        $feed_repo = new FeedRepository($this->config);

        if ($feed_type === 'public') {
            $feeds = $feed_repo->getAll();
        } elseif ($feed_type === 'user') {
            $feeds = $feed_repo->getAllByUser($user);
        } else {
            throw new \InvalidArgumentException();
        }

        $total_feeds = count($feeds);

        if ($limit && is_numeric($limit)) {
            $feeds = array_slice($feeds, 0, $limit);
        }

        return $this->twig->render(sprintf("%sfeeds.html.twig", $feed_type), array(
            'total_feeds' => $total_feeds,
            'feeds' => $feeds,
            'user_id' => $user->getId(),
        ));
    }

    public function loadPublicFeedsIncludeAction($limit = 'all')
    {
        try {
            return $this->loadInclude($limit, 'public');
        } catch (\InvalidArgumentException $e) {
            return json_encode(array('error' => $e->getMessage()));
        }
    }

    /**
     * @return string
     */
    public function getFeedAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['feed_id'])) {
                $feed_id = $_POST['feed_id'];

                if (is_numeric($feed_id)) {
                    //TODO santise feed id
                    $feed_id = (int)$feed_id;
                    $feed_repo = new FeedRepository($this->config);

                    try {
                        $feed = $feed_repo->getOne($feed_id);
                    } catch (FeedIdNotFoundException $e) {
                        return $this->twig->render('error.html.twig', array('error_code' => null, 'message' => $e->getMessage()));
                    }

                    $rss = RssValidator::validateDoc($feed->getUrl());

                    $items_raw = $rss->getElementsByTagName('item');
                    $items = array();
                    foreach ($items_raw as $it) {
                        $item = new Item();

                        $item->setTitle($it->getElementsByTagName('title')->item(0)->nodeValue);
                        $item->setDescription($it->getElementsByTagName('description')->item(0)->nodeValue);
                        $item->setDate(\DateTime::createFromFormat(\DateTime::RSS, $it->getElementsByTagName('pubDate')->item(0)->nodeValue));
                        $item->setLink($it->getElementsByTagName('link')->item(0)->nodeValue);
                        $items[] = $item;
                    }
                    $feed->setItems($items);

                    return $this->twig->render('rssview.html.twig', array('feed' => $feed));
                }
            }
        }

    }


}