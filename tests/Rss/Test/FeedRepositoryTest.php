<?php

namespace Rss\Test;

use Rss\Database;
use Rss\Model\Feed;
use Rss\Model\User;
use Rss\Repo\FeedRepository;
use Rss\Repo\UserRepository;


class FeedRepositoryTest extends TestCase
{

    public function testInsertInt()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');
        $feed_repo = new FeedRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\FeedRepository', $feed_repo);

        $feed = 12345;
        $feed_repo->insert($feed);

    }

    public function testInsertArray()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');
        $feed_repo = new FeedRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\FeedRepository', $feed_repo);

        $feed = array(
            'title' => 'BBC News Feed',
            'url' => 'http://feeds.bbci.co.uk/news/rss.xml',
            'user_id' => 5,
        );
        $feed_repo->insert($feed);
    }

    public function testInsertString()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');
        $feed_repo = new FeedRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\FeedRepository', $feed_repo);

        $feed = "BBC";
        $feed_repo->insert($feed);
    }

    public function testInsertUser()
    {
        $this->setExpectedException('InvalidArgumentException');
        $feed_repo = new FeedRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\FeedRepository', $feed_repo);

        $feed = new User();
        $feed_repo->insert($feed);
    }


    public function testInsertAndDeleteValidData()
    {
        $user_repo = new UserRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\UserRepository', $user_repo);

        //create and add user
        $user = new User();
        $inserted_user_id = $user_repo->insert($user);
        $this->assertInternalType('int', $inserted_user_id);


        $feed_repo = new FeedRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\FeedRepository', $feed_repo);


        $feed = new Feed();
        $feed->setTitle('BBC News Feed')
            ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
            ->setUserId($inserted_user_id);

        $inserted = $feed_repo->insert($feed);
        $this->assertInternalType('int', $inserted);

        $deleted = $feed_repo->delete($inserted);
        $expected = 1;
        $this->assertEquals($expected, $deleted);
    }

    public function testInsertUnknownUserIDWithFeed()
    {
        $user_id = 999999;
        $feed = new Feed();
        $feed->setTitle('BBC News Feed')
            ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
            ->setUserId($user_id);

        $feed_repo = new FeedRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\FeedRepository', $feed_repo);

        $this->setExpectedException('Rss\Exception\UserIdNotFoundException');
        $feed_repo->insert($feed);

    }

    public function testDeleteUsingID()
    {
        $user = new User();
        $user_repo = new UserRepository($this->config);
        $user_id = $user_repo->insert($user);
        $this->assertInternalType('int', $user_id);


        $feed_repo = new FeedRepository($this->config);
        $feed = new Feed();
        $feed->setTitle('BBC News Feed')
            ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
            ->setUserId($user_id);


        $inserted = $feed_repo->insert($feed);
        $this->assertInternalType('int', $inserted);


        $deleted = $feed_repo->delete($inserted);
        $this->assertTrue($deleted);
    }

    public function testDeleteNonExistantID()
    {
        $id = 12345;
        $this->setExpectedException('Rss\Exception\FeedIdNotFoundException');
        $feed_repo = new FeedRepository($this->config);
        $deleted = $feed_repo->delete($id);
    }

    public function testDeleteUsingArray()
    {
        $feed_repo = new FeedRepository($this->config);
        $this->setExpectedException('InvalidArgumentException');
        $deleted = $feed_repo->delete(array('12345'));
    }

    public function testDeleteUsingObject()
    {
        $obj = (object)array('url' => 'http://google.com');
        $feed_repo = new FeedRepository($this->config);
        $this->setExpectedException('InvalidArgumentException');
        $deleted = $feed_repo->delete($obj);
    }


    //Test SELECT

    public function createSomeRecords($total = 20)
    {
        $user_repo = new UserRepository($this->config);

        $user = new User();
        $inserted_user_id = $user_repo->insert($user);

        $feed_repo = new FeedRepository($this->config);

        for ($i = 0; $i < $total; $i++) {
            $feed = new Feed();
            $feed->setTitle('BBC News Feed')
                ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
                ->setUserId($inserted_user_id);
            $feed_repo->insert($feed);
        }
    }


    public function testGetAll()
    {
        $user_repo = new UserRepository($this->config);
        $db = Database::connect($this->config['db']);

        $user_repo = new UserRepository($this->config);

        $user = new User();
        $inserted_user_id = $user_repo->insert($user);

        //Delete all feeds
        $db->query("DELETE FROM feeds WHERE 1");

        $feed_repo = new FeedRepository($this->config);

        //Create 12 records
        for ($i = 0; $i < 12; $i++) {
            $feed = new Feed();
            $feed->setTitle('BBC News Feed')
                ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
                ->setUserId($inserted_user_id);
            $feed_repo->insert($feed);
        }

        $feeds = $feed_repo->getAll();
        $expected = 12;
        $this->assertInternalType('array', $feeds);
        $this->assertEquals($expected, count($feeds));

        foreach ($feeds as $feed) {
            $this->assertInstanceOf('Rss\Model\Feed', $feed);
        }
    }

    public function testGetAllWithLimit()
    {

        $db = Database::connect($this->config['db']);

        $user_repo = new UserRepository($this->config);

        $user = new User();
        $inserted_user_id = $user_repo->insert($user);

        //Delete all feeds
        $db->query("DELETE FROM feeds WHERE 1");

        $feed_repo = new FeedRepository($this->config);

        //Create 12 records
        for ($i = 0; $i < 20; $i++) {
            $feed = new Feed();
            $feed->setTitle('BBC News Feed')
                ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
                ->setUserId($inserted_user_id);
            $feed_repo->insert($feed);
        }

        $feeds = $feed_repo->getAll(10);
        $expected = 10;
        $this->assertInternalType('array', $feeds);
        $this->assertEquals($expected, count($feeds));

        foreach ($feeds as $feed) {
            $this->assertInstanceOf('Rss\Model\Feed', $feed);
        }
        $db->query("DELETE FROM feeds WHERE 1");
    }

    public function testGetAllWithStringLimitParam()
    {
        $this->setExpectedException('InvalidArgumentException');
        $feed_repo = new FeedRepository($this->config);
        $feeds = $feed_repo->getAll("10");
    }

    public function testGetAllWithStringLimitParam2()
    {
        $this->setExpectedException('InvalidArgumentException');
        $feed_repo = new FeedRepository($this->config);
        $feeds = $feed_repo->getAll("a string");
    }

    public function testGetAllWithArrayLimitParam()
    {
        $this->setExpectedException('InvalidArgumentException');
        $feed_repo = new FeedRepository($this->config);
        $feeds = $feed_repo->getAll(array(10));
    }

    public function testGetAll0Records()
    {
        $db = Database::connect($this->config['db']);
        $db->query("DELETE FROM feeds WHERE 1");
        $feed_repo = new FeedRepository($this->config);
        $feeds = $feed_repo->getAll(10);

        $expected = 0;
        $this->assertInternalType('array', $feeds);
        $this->assertEquals($expected, count($feeds));

        $feeds = $feed_repo->getAll();

        $expected = 0;
        $this->assertInternalType('array', $feeds);
        $this->assertEquals($expected, count($feeds));
    }

    public function testGetAllByUser()
    {
        $db = Database::connect($this->config['db']);

        $user_repo = new UserRepository($this->config);

        $user = new User();
        $inserted_user_id = $user_repo->insert($user);
        $user->setId($inserted_user_id);

        $user2 = new User();
        $inserted_user_id2 = $user_repo->insert($user2);
        $user2->setId($inserted_user_id2);

        //Delete all feeds
        $db->query("DELETE FROM feeds WHERE 1");

        $feed_repo = new FeedRepository($this->config);


        for ($i = 0; $i < 5; $i++) {
            $feed = new Feed();
            $feed->setTitle('BBC News Feed')
                ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
                ->setUserId($inserted_user_id);
            $feed_repo->insert($feed);
        }
        for ($i = 0; $i < 10; $i++) {
            $feed = new Feed();
            $feed->setTitle('BBC News Feed')
                ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
                ->setUserId($inserted_user_id2);
            $feed_repo->insert($feed);
        }

        $feeds = $feed_repo->getAllByUser($user);
        $expected = 5;
        $this->assertInternalType('array', $feeds);
        $this->assertEquals($expected, count($feeds));

        foreach ($feeds as $feed) {
            $this->assertInstanceOf('Rss\Model\Feed', $feed);
            $this->assertEquals($inserted_user_id, $feed->getUserID());
        }

        $feeds = $feed_repo->getAllByUser($user2);
        $expected = 10;
        $this->assertInternalType('array', $feeds);
        $this->assertEquals($expected, count($feeds));

        foreach ($feeds as $feed) {
            $this->assertInstanceOf('Rss\Model\Feed', $feed);
            $this->assertEquals($inserted_user_id2, $feed->getUserID());
        }
        $db->query("DELETE FROM feeds WHERE 1");
    }

    public function testGetAllByUserAndLimit()
    {
        $db = Database::connect($this->config['db']);

        $user_repo = new UserRepository($this->config);

        $user = new User();
        $inserted_user_id = $user_repo->insert($user);
        $user->setId($inserted_user_id);

        $user2 = new User();
        $inserted_user_id2 = $user_repo->insert($user2);
        $user2->setId($inserted_user_id2);

        //Delete all feeds
        $db->query("DELETE FROM feeds WHERE 1");

        $feed_repo = new FeedRepository($this->config);


        for ($i = 0; $i < 5; $i++) {
            $feed = new Feed();
            $feed->setTitle('BBC News Feed')
                ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
                ->setUserId($inserted_user_id);
            $feed_repo->insert($feed);
        }
        for ($i = 0; $i < 10; $i++) {
            $feed = new Feed();
            $feed->setTitle('BBC News Feed')
                ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
                ->setUserId($inserted_user_id2);
            $feed_repo->insert($feed);
        }

        $feeds = $feed_repo->getAllByUser($user, 2);
        $expected = 2;
        $this->assertInternalType('array', $feeds);
        $this->assertEquals($expected, count($feeds));

        foreach ($feeds as $feed) {
            $this->assertInstanceOf('Rss\Model\Feed', $feed);
            $this->assertEquals($inserted_user_id, $feed->getUserID());
        }

        $feeds = $feed_repo->getAllByUser($user2, 9);
        $expected = 9;
        $this->assertInternalType('array', $feeds);
        $this->assertEquals($expected, count($feeds));

        foreach ($feeds as $feed) {
            $this->assertInstanceOf('Rss\Model\Feed', $feed);
            $this->assertEquals($inserted_user_id2, $feed->getUserID());
        }
        $db->query("DELETE FROM feeds WHERE 1");
    }

    public function testGetAllByUserWithBadParams1()
    {
        $feed_repo = new FeedRepository($this->config);
        $this->setExpectedException('PHPUnit_Framework_Error');
        $feed = $feed_repo->getAllByUser(array());

    }

    public function testGetAllByUserWithBadParams2()
    {
        $feed_repo = new FeedRepository($this->config);
        $this->setExpectedException('PHPUnit_Framework_Error');

        $feed = new Feed();
        $feed = $feed_repo->getAllByUser($feed);

    }
    public function testGetAllByUserWithBadParams3()
    {
        $feed_repo = new FeedRepository($this->config);
        $this->setExpectedException('PHPUnit_Framework_Error');


        $feed = $feed_repo->getAllByUser(12345);

    }
    public function testGetAllByUserWithBadParams4()
    {
        $feed_repo = new FeedRepository($this->config);

        $this->setExpectedException('InvalidArgumentException');
        $user = new User();
        $feed = $feed_repo->getAllByUser($user, "One Hundred");
    }

}