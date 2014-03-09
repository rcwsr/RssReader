<?php

namespace Rss\Controller;

/**
 * Class Controller
 * @package Rss\Controller
 */
class Controller
{
    protected $twig;
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;

        $loader = new \Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . "/../views");
        $this->twig = new \Twig_Environment($loader);
    }
} 