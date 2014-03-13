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

    public function errorAction($code, $message)
    {
        return $this->twig->render('error.html.twig', array('error_code' => $code, 'message' => $message));
    }

} 