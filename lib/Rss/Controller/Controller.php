<?php

namespace Rss\Controller;

/**
 * Class Controller
 *
 * Abstract class that all controllers inherit. Loads config file from app.php and loads Twig so twig template
 * responses can be given -  Extending controllers then don't have to worry about loading config file or twig.
 *
 * @package Rss\Controller
 */
abstract class Controller
{
    protected $twig;
    protected $config;

    /**
     * @param $config json config file.
     */
    public function __construct($config)
    {
        $this->config = $config;

        //Load twig
        $loader = new \Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . "/../views");
        $this->twig = new \Twig_Environment($loader);
    }
} 