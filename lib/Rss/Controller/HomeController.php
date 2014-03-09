<?php

namespace Rss\Controller;

use Rss\Database;

class HomeController extends Controller
{
    public function indexGetAction()
    {
        $db = Database::connect($this->config['db']);
        return $this->twig->render('index.html.twig', array('name' => 'Fabien'));
    }

    public function indexPostAction()
    {

    }
}