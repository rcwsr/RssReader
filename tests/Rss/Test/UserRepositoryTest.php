<?php

namespace Rss\Test;

use Rss\Exception\DuplicateUserHashException;
use Rss\Model\Feed;
use Rss\Model\User;
use Rss\Repo\UserRepository;

class UserRepositoryTest extends TestCase
{

    public function testInsertInt()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');
        $user_repo = new UserRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\UserRepository', $user_repo);

        $user = 12345;
        $user_repo->insert($user);

    }

    public function testInsertArray()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');
        $user_repo = new UserRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\UserRepository', $user_repo);

        $user = array('hash' => "abcdefgh");

        $user_repo->insert($user);
    }

    public function testInsertString()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');
        $user_repo = new UserRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\UserRepository', $user_repo);

        $user = "bbbbbbbbb";
        $user_repo->insert($user);
    }

    public function testInsertFeed()
    {
        $this->setExpectedException('InvalidArgumentException');
        $user_repo = new UserRepository($this->config);
        $this->assertInstanceOf('Rss\Repo\UserRepository', $user_repo);

        $user = new Feed();
        $user->setTitle('BBC News Feed')
            ->setUrl('http://feeds.bbci.co.uk/news/rss.xml')
            ->setUserId(12355);
        $user_repo->insert($user);
    }

    public function testInsertValidData()
    {
        $user_repo = new UserRepository($this->config);
        $user = new User();
        $inserted = $user_repo->insert($user);
        $this->assertInternalType('int', $inserted);
    }

    public function testInsertDuplicateUserHash()
    {
        $user_repo = new UserRepository($this->config);

        $user = new User();
        $hash = $user->getHash();

        $user_repo->insert($user);

        $user2 = new User();
        $user2->setHash($hash);

        $this->setExpectedException('Rss\Exception\DuplicateUserHashException');
        $user_repo->insert($user2);

    }



    public function testDeleteUsingHash()
    {
        $user_repo = new UserRepository($this->config);


        $user = new User();
        $hash = $user->getHash();

        $inserted = $user_repo->insert($user);
        $this->assertInternalType('int', $inserted);


        $deleted = $user_repo->delete($hash);
        $this->assertTrue($deleted);
        //$this->setExpectedException('UserIdNotFoundException');
    }

    public function testDeleteUsingID()
    {
        $user_repo = new UserRepository($this->config);

        $user = new User();

        $inserted = $user_repo->insert($user);
        $this->assertInternalType('int', $inserted);

        $id = $inserted;

        $deleted = $user_repo->delete($id);
        $this->assertTrue($deleted);
    }

    public function testDeleteUsingArray()
    {
        $user_repo = new UserRepository($this->config);
        $this->setExpectedException('InvalidArgumentException');
        $deleted = $user_repo->delete(array('12345'));
    }

    public function testDeleteUsingObject()
    {
        $obj = (object)array('name' => 'Robin');
        $user_repo = new UserRepository($this->config);
        $this->setExpectedException('InvalidArgumentException');
        $deleted = $user_repo->delete($obj);
    }

    public function testDeleteNonExistantID()
    {
        $id = 12345;
        $this->setExpectedException('Rss\Exception\UserIdNotFoundException');
        $user_repo = new UserRepository($this->config);
        $deleted = $user_repo->delete($id);

    }

    public function testDeleteNonExistantHash()
    {
        $hash = "ZZZZZZZZ";
        $this->setExpectedException('Rss\Exception\UserIdNotFoundException');
        $user_repo = new UserRepository($this->config);
        $deleted = $user_repo->delete($hash);
    }

}