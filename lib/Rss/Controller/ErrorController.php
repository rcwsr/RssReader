<?php


namespace Rss\Controller;

/**
 * Class ErrorController
 *
 * Class to handle error responses. Uses a single method where the error code and message are given.
 *
 * @package Rss\Controller
 */
class ErrorController extends Controller
{
    /**
     * returns an error page
     *
     * @param $code error code
     * @param $message error message
     * @return string twig html response
     */
    public function errorAction($code, $message)
    {
        return $this->twig->render('error.html.twig', array('error_code' => $code, 'message' => $message));
    }

} 