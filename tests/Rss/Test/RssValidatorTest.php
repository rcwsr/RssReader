<?php
/**
 * Created by PhpStorm.
 * User: robincawser
 * Date: 10/03/2014
 * Time: 19:46
 */

namespace Rss\Test;


use Rss\Validator\RssValidator;

class RssValidatorTest extends TestCase
{
    public function testValidateUrl1()
    {
        $url = "http://open.live.bbc.co.uk/weather/feeds/en/le3/3dayforecast.rss";
        $this->assertTrue(RssValidator::validateUrl($url));
    }

    public function testValidateUrl2()
    {
        $url = "open.live.bbc.co.uk/weather/feeds/en/le3/3dayforecast.rss";
        $this->assertFalse(RssValidator::validateUrl($url));
    }

    public function testValidateUrl3()
    {
        $url = "something not a url";
        $this->assertFalse(RssValidator::validateUrl($url));
    }

    public function testValidateUrl4()
    {
        $url = 1234;
        $this->assertFalse(RssValidator::validateUrl($url));
    }

    public function testValidateUrl5()
    {
        $url = "robincawser@gmail.com";
        $this->assertFalse(RssValidator::validateUrl($url));
    }

    public function testValidateDoc1(){
        $url = "http://open.live.bbc.co.uk/weather/feeds/en/le3/3dayforecast.rss";
        $this->assertInstanceOf('\DOMDocument', RssValidator::validateDoc($url));
    }

    public function testValidateDoc2(){
        $url = "http://php.net/news.rss";
        $this->assertInstanceOf('\DOMDocument', RssValidator::validateDoc($url));
    }

    public function testValidateDoc3(){
        $url = "http://www.telegraph.co.uk/news/uknews/rss";
        $this->assertInstanceOf('\DOMDocument', RssValidator::validateDoc($url));
    }

    public function testValidateDoc4(){
        $url = "http://feeds.feedburner.com/cyclingtipsblog/TJog?format=xml";
        $this->assertInstanceOf('\DOMDocument', RssValidator::validateDoc($url));
    }

    public function testValidateDoc5(){
        $url = "http://rss.slashdot.org/Slashdot/slashdot";
        $this->assertInstanceOf('\DOMDocument', RssValidator::validateDoc($url));
    }

    public function testValidateDocWithNonRSSFeed1(){
        $url = "http://www.youtube.com/watch?v=Y52mDF2ofHA";
        $this->assertFalse(RssValidator::validateDoc($url));
    }

    public function testValidateDocWithNonRSSFeed2(){
        $url = "http://uk3.php.net/domdocument.load";
        $this->assertFalse(RssValidator::validateDoc($url));
    }


} 