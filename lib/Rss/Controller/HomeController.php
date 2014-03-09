<?php

namespace Rss\Controller;

class HomeController extends Controller
{

    /**
     * @param $twig_path
     */
    public function __construct($twig_path)
    {
        parent::__construct($twig_path);
    }

    public function indexAction()
    {
        return $this->twig->render('index.html.twig', array('name' => 'Fabien'));
    }
}