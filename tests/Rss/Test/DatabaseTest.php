<?php
/**
 * Created by PhpStorm.
 * User: robincawser
 * Date: 09/03/2014
 * Time: 16:42
 */

namespace Rss\Test;


use Rss\Database;

class DatabaseTest extends TestCase
{
    public function testConfigNotArray(){
        $this->setExpectedException('InvalidArgumentException');

        $test = 12345;
        $db = Database::connect($test);
    }

    public function testConfigNotArray2(){
        $this->setExpectedException('InvalidArgumentException');

        $test = "password";
        $db = Database::connect($test);
    }


} 