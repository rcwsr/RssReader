<?php

namespace Rss\Test;

use Rss\Repo\FeedRepository;


class FeedRepositoryTest extends TestCase
{
    private $inserted_ids = array();

    public function testInsertNonArray()
    {
        $this->setExpectedException('InvalidArgumentException');
        $feed_repo = new FeedRepository($this->config);

        $data = 12345;
        $feed_repo->insert($data);

    }

    public function testInsertNonArray2()
    {
        $this->setExpectedException('InvalidArgumentException');
        $feed_repo = new FeedRepository($this->config);

        $data = "data";
        $feed_repo->insert($data);
    }

    public function testInsertAndDeleteValidData()
    {
        $feed_repo = new FeedRepository($this->config);
        $data = array(
            'title' => 'BBC News Feed',
            'url' => 'http://feeds.bbci.co.uk/news/rss.xml'
        );
        $inserted = $feed_repo->insert($data);
        $this->assertInternalType('int', $inserted);

        $deleted = $feed_repo->delete($inserted);
        $this->assertTrue($deleted);

    }

    public function testInsertInvalidKeys1()
    {
        $this->setExpectedException('Rss\Exception\InvalidDBParameterException');
        $feed_repo = new FeedRepository($this->config);
        $data = array(
            'invalid_key' => 'BBC News Feed',
            'url' => 'http://feeds.bbci.co.uk/news/rss.xml'
        );
        $inserted = $feed_repo->insert($data);
    }

    public function testInsertInvalidKeys2()
    {
        $this->setExpectedException('Rss\Exception\InvalidDBParameterException');
        $feed_repo = new FeedRepository($this->config);
        $data = array();
        $inserted = $feed_repo->insert($data);
    }

    public function testInsertInvalidKeys3()
    {
        $this->setExpectedException('Rss\Exception\InvalidDBParameterException');
        $feed_repo = new FeedRepository($this->config);
        $data = array(
            'url' => 'http://feeds.bbci.co.uk/news/rss.xml'
        );
        $inserted = $feed_repo->insert($data);
    }


}