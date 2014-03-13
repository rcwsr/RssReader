<?php
/**
 * Created by PhpStorm.
 * User: robincawser
 * Date: 10/03/2014
 * Time: 18:33
 */

namespace Rss\Validator;


class RssValidator
{

    /**
     * @param $url
     * @return bool
     */
    public static function validateUrl($url)
    {
        return (bool)filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param $url
     * @return bool|\DOMDocument
     */
    public static function validateDoc($url)
    {

        $rss = new \DOMDocument();
        $rss->strictErrorChecking = false;
        $rss->recover = true;

        if (!$rss->load($url, LIBXML_NOERROR|LIBXML_NOWARNING)) {
            return false;
        }
        if ($rss->getElementsByTagName('title')->item(0) instanceof \DOMNode) {
            if ($rss->getElementsByTagName('item')->item(0) instanceof \DOMNode) {

                return $rss;
            }
        } else {
            return false;
        }
    }

} 