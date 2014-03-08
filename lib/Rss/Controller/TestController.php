<?php

namespace Rss\Controller;

class TestController extends Controller
{

    public function testMethod()
    {
        return $this->twig->render('index.html', array('name' => 'Fabien'));
    }

    public function test2Method()
    {
        return $this->twig->render('index.html', array('name' => 'Fabien'));
    }
}