<?php

namespace Rss\Test;

class TestCase extends \PHPUnit_Framework_TestCase{

    protected $config;

    public function __construct(){
        $this->config = json_decode(file_get_contents(__DIR__ . '/../../../config/config.json'), true);
    }
} 