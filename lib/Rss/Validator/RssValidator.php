<?php


namespace Rss\Validator;

/**
 * Class RssValidator
 * @package Rss\Validator
 */
class RssValidator
{

    /**
     * @param $url
     * @return bool|\DOMDocument
     */
    public static function validateDoc($url)
    {
        if (!RssValidator::validateUrl($url)) {
            return false;
        }

        $validator = 'http://feedvalidator.org/check.cgi?url=';


        $rss = new \DOMDocument();
        $rss->strictErrorChecking = false;
        $rss->recover = true;

        if (!$rss->load($url, LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return false;
        }

        if ($rss->getElementsByTagName('channel')->length > 0 && $rss->getElementsByTagName('item')->length > 0) {


            return $rss;


        } else {
            return false;
        }
    }

    /**
     * @param $url
     * @return bool
     */
    public static function validateUrl($url)
    {
        return (bool)filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param \DomElement $element
     * @return null|string
     */
    public static function validateDescription(\DomElement $element)
    {
        $description = $element->getElementsByTagName('description');
        if ($description->length == 0) {
            return null;
        } else {
            return $description->item(0)->nodeValue;
        }
    }

    /**
     * @param \DomElement $element
     * @return \DateTime|null
     */
    public static function validateDate(\DomElement $element)
    {

        $pubdate = $element->getElementsByTagName('pubdate');

        $namespace = 'http://purl.org/dc/elements/1.1/';
        $date = $element->getElementsByTagNameNS($namespace, 'date');
        if ($date->length == 0 && $pubdate->length == 0) {
            return null;
        }

        if ($pubdate->length == 1) {
            return \DateTime::createFromFormat(\DateTime::RSS, $pubdate->item(0)->nodeValue);
        } elseif ($date->length == 1) {

            return \DateTime::createFromFormat('Y-m-d', $date->item(0)->nodeValue);
        } else {
            return null;
        }
    }

    /**
     * @param \DomElement $element
     * @return null|string
     */
    public static function validateTitle(\DomElement $element)
    {
        $title = $element->getElementsByTagName('title');
        if ($title->length == 0) {
            return null;
        } else {
            return $title->item(0)->nodeValue;
        }
    }

    /**
     * @param \DOMDocument $doc
     * @return bool|string
     */
    public static function validateDocTitle(\DOMDocument $doc)
    {
        $title = $doc->getElementsByTagName('title');
        if($title->length == 0) {
            return false;
        }
        else{
            return $title->item(0)->nodeValue;
        }
    }

    /**
     * @param \DomElement $element
     * @return null|string
     */
    public static function validateLink(\DomElement $element)
    {
        $link = $element->getElementsByTagName('link');
        if ($link->length == 0) {
            return null;
        } else {
            if (RssValidator::validateUrl($link->item(0)->nodeValue)) {
                return $link->item(0)->nodeValue;
            } else {
                return null;
            }
        }
    }

} 