<?php

namespace Rss\Controller;

/**
 * Class Controller
 * @package Rss\Controller
 */
class Controller
{
    protected $twig;

    public function __construct($twig_path)
    {
        $loader = new \Twig_Loader_Filesystem($twig_path);
        $this->twig = new \Twig_Environment($loader);
    }
} 