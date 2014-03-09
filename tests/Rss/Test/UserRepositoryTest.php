<?php

namespace Rss\Test;

use Rss\Exception\DuplicateUserHashException;
use Rss\Repo\UserRepository;

class UserRepositoryTest extends TestCase
{

    public function testInsertNonArray()
    {
        $this->setExpectedException('InvalidArgumentException');
        $user_repo = new UserRepository($this->config);

        $data = 12345;
        $user_repo->insert($data);

    }

    public function testInsertNonArray2()
    {
        $this->setExpectedException('InvalidArgumentException');
        $user_repo = new UserRepository($this->config);

        $data = "data";
        $user_repo->insert($data);
    }

    public function testInsertValidData()
    {
        $user_repo = new UserRepository($this->config);
        $data = array(
            'hash' => 'userHash',
        );
        $inserted = $user_repo->insert($data);
        $this->assertInternalType('int', $inserted);


        $deleted = $user_repo->delete('userHash');
        $this->assertTrue($deleted);
    }

    public function testInsertDuplicateUserHash()
    {
        $user_repo = new UserRepository($this->config);

        $data1 = array(
            'hash' => 'abcdefgh',
        );

        $data2 = array(
            'hash' => 'abcdefgh',
        );


        try {
            $inserted1 = $user_repo->insert($data1);
            $this->assertInternalType('int', $inserted1);
        } catch (DuplicateUserHashException $e) {
            $deleted = $user_repo->delete('abcdefgh');
            $this->assertTrue($deleted);

            $inserted1 = $user_repo->insert($data1);
            $this->assertInternalType('int', $inserted1);
        }


        $this->setExpectedException('Rss\Exception\DuplicateUserHashException');

        $inserted2 = $user_repo->insert($data2);

        $deleted = $user_repo->delete('abcdefgh');
        $this->assertTrue($deleted);
    }

    public function testInsertInvalidKeys1()
    {
        $this->setExpectedException('Rss\Exception\InvalidDBParameterException');
        $user_repo = new UserRepository($this->config);
        $data = array(
            'invalid_key' => 'userHash',
        );
        $inserted = $user_repo->insert($data);
    }

    public function testInsertInvalidKeys2()
    {
        $this->setExpectedException('Rss\Exception\InvalidDBParameterException');
        $user_repo = new UserRepository($this->config);
        $data = array();
        $inserted = $user_repo->insert($data);
    }

    public function testInsertInvalidKeys3()
    {
        $this->setExpectedException('Rss\Exception\InvalidDBParameterException');
        $user_repo = new UserRepository($this->config);
        $data = array(
            'url' => 'http://feeds.bbci.co.uk/news/rss.xml'
        );
        $inserted = $user_repo->insert($data);
    }

    public function testDeleteUsingHash()
    {
        $user_repo = new UserRepository($this->config);


        $data = array(
            'hash' => 'AAAAAAAA',
        );

        $inserted = $user_repo->insert($data);
        $this->assertInternalType('int', $inserted);

        $id = 'AAAAAAAA';

        $deleted = $user_repo->delete($id);
        $this->assertTrue($deleted);
    }

    public function testDeleteUsingID()
    {
        $user_repo = new UserRepository($this->config);

        $data = array(
            'hash' => 'BBBBBBBB',
        );

        $inserted = $user_repo->insert($data);
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
        $obj = (object) array('name' => 'Robin');
        $user_repo = new UserRepository($this->config);
        $this->setExpectedException('InvalidArgumentException');
        $deleted = $user_repo->delete($obj);
    }

}