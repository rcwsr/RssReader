<?php

namespace Rss\Controller;

use Rss\Exception\FeedIdNotFoundException;
use Rss\Exception\UserIdNotFoundException;
use Rss\Model\Feed;
use Rss\Model\Item;
use Rss\Provider\UserProvider;
use Rss\Repo\FeedRepository;
use Rss\Validator\RssValidator;

/**
 * Class AjaxController
 *
 * This class handles all ajax requests and gives appropriate json or template responses. Here feeds can be added,
 * deleted and retrieved.
 *
 * @package Rss\Controller
 */
class AjaxController extends Controller
{
    /**
     * As long as the request is POST, this method attempts to add a feed to the database. The URL of the feed is
     * taken from POST data, validated and added to the database along with the current user's id. If the URL fails
     * to validate, json responses are given with relevant error messages, though in production, these messages should
     * be limited in the verbosity. Upon a successful feed addition a json success message is given.
     *
     * @return string json response
     * @throws \Exception
     */
    public function addFeedAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['url'])) {

                $url = $_POST['url'];

                if (RssValidator::validateUrl($url)) {

                    //validate url
                    $rss = RssValidator::validateDoc($url);

                    if ($rss) {

                        //Get current user
                        $user_provider = new UserProvider($this->config);
                        $user = $user_provider->getUser();

                        //Get the title from
                        $title = RssValidator::validateDocTitle($rss);

                        //create new feed object
                        $feed = new Feed();
                        $feed->setTitle($title)
                            ->setUrl($url)
                            ->setUserId($user->getId());

                        $feed_repo = new FeedRepository($this->config);
                        try {
                            //Add feed to db
                            $feed_repo->insert($feed);
                            return json_encode(array('success' => "Feed added successfully"));
                        } catch (UserIdNotFoundException $e) {
                            //return error if the user is not found
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
     * Deletes an rss feed given a feed id in POST data. If feed's user id does not match current user's id, an error
     * is given. Returns json responses upon success or error.
     *
     * @return string json response
     */
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
     * Displays a confirmation page for deleting a feed. Returns the html for the deletefeed template
     * @return string twig template response
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
     * Loads a template showing all user feeds depending on a given limit if the limit param is either numeric
     * or 'all'
     *
     * @param string $limit The amount of feeds to load
     * @return string returns either a json response or html page
     */
    public function loadUserFeedsIncludeAction($limit = 'all')
    {
        try {
            return $this->loadInclude($limit, 'user');
        } catch (\InvalidArgumentException $e) {
            return json_encode(array('error' => $e->getMessage()));
        }
    }

    /**
     * Loads a template showing all public feeds depending on a given limit if the limit param is either numeric
     * or 'all'
     *
     * @param string $limit The amount of feeds to load
     * @return string returns either a json response or html page
     */
    public function loadPublicFeedsIncludeAction($limit = 'all')
    {
        try {
            return $this->loadInclude($limit, 'public');
        } catch (\InvalidArgumentException $e) {
            return json_encode(array('error' => $e->getMessage()));
        }
    }

    /**
     * Method is called when loading public or user feed page includes. Provided the limit is numeric or 'all', loads
     * the required amount of feeds for relevent feed type.
     *
     * @param $limit amount of feeds to load
     * @param $feed_type either user or public
     * @return string a twig html template
     * @throws \InvalidArgumentException
     */
    private function loadInclude($limit, $feed_type)
    {
        //ensure $limit parameter is either numeric, or is 'all'
        if (!is_numeric($limit) && $limit !== 'all') {
            return json_encode(array('error' => "Limit must be an int"));
        }

        //Get current user
        $user_provider = new UserProvider($this->config);
        $user = $user_provider->getUser();


        $feed_repo = new FeedRepository($this->config);

        //Get either public or user feeds depending on what is specified by $feed_type
        if ($feed_type === 'public') {
            $feeds = $feed_repo->getAll();
        } elseif ($feed_type === 'user') {
            $feeds = $feed_repo->getAllByUser($user);
        } else {
            throw new \InvalidArgumentException();
        }

        //Count total feeds so that the html can display a total
        $total_feeds = count($feeds);

        //Not the most efficient way of getting a limited records (should use LIMIT in sql).
        //Slices array of feeds if $limit is set.
        if ($limit && is_numeric($limit)) {
            $feeds = array_slice($feeds, 0, $limit);
        }

        return $this->twig->render(sprintf("%sfeeds.html.twig", $feed_type), array(
            'total_feeds' => $total_feeds,
            'feeds' => $feeds,
            'user_id' => $user->getId(),
        ));
    }


    /**
     * Get's a single feed depending on the feed_id set in POST data. Provided the feed exists will return the feed
     * template page. Otherwise an error page is shown.
     *
     * @return string feed template, or error page if feed not found
     */
    public function getFeedAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['feed_id'])) {
                $feed_id = $_POST['feed_id'];

                if (is_numeric($feed_id)) {

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
                        $item->setTitle(RssValidator::validateTitle($it));
                        $item->setDescription(RssValidator::validateDescription($it));
                        $item->setDate(RssValidator::validateDate($it));
                        $item->setLink(RssValidator::validateLink($it));
                        $items[] = $item;
                    }
                    $feed->setItems($items);

                    return $this->twig->render('rssview.html.twig', array('feed' => $feed));
                }
            }
        }

    }


}