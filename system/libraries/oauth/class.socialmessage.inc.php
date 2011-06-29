<?php

/**
 * This file contains a class to save a common message
 * object for all the social networks
 *
 * PHP Version 5.3
 *
 * @category   Libraries
 * @package    OAuth
 * @subpackage Libraries
 * @author     M2Mobi <info@m2mobi.com>
 * @author     Julio Foulquié <julio@m2mobi.com>
 */

/**
 * Social Message Class
 *
 * @category   Libraries
 * @package    OAuth
 * @subpackage Libraries
 * @author     M2Mobi <info@m2mobi.com>
 * @author     Julio Foulquié <julio@m2mobi.com>
 */
class SocialMessage
{

    /**
     * Message to be posted
     * @var String
     */
    private $message;

    /**
     * Comment for the main message
     * @var String
     */
    private $comment;

    /**
     * URL to append to the message
     * @var String
     */
    private $url;

    /**
     * URL for an image to add to the message
     * @var String
     */
    private $image_url;

    /**
     * Visibility properties for the current message
     * @var String
     */
    private $visibility;


    /**
     * Constructor.
     */
    public function __construct($msg = '')
    {
        $this->message = $msg;
    }

     /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->message);
        unset($this->comment);
        unset($this->url);
        unset($this->image_url);
        unset($this->visibility);
    }

    public function __set($key, $val)
    {
        $this->$key = $val;
    }

    public function __get($key)
    {
        return $this->$key;
    }

}

?>