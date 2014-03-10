<?php
/**
 * Created by PhpStorm.
 * User: robincawser
 * Date: 08/03/2014
 * Time: 22:54
 */

namespace Rss\Controller;


class ErrorController extends Controller
{
    public function __construct($twig_path)
    {
        parent::__construct($twig_path);
    }

    public function _404()
    {
        return $this->twig->render('error.html.twig', array('error_code' => 404, 'message' => null));
    }

    public function _403()
    {
        header('HTTP/1.0 403 Forbidden');
        return $this->twig->render('error.html.twig', array('error_code' => 404, 'message' => null));
    }
} 