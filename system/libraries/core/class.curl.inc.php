<?php

/**
 * This file contains a Curl wrapper class.
 *
 * PHP Version 5.3
 *
 * @category   Libraries
 * @package    Core
 * @subpackage Libraries
 * @author     M2Mobi <info@m2mobi.com>
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 */

/**
 * Curl Class
 *
 * @category   Libraries
 * @package    Core
 * @subpackage Libraries
 * @author     M2Mobi <info@m2mobi.com>
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 */
class Curl
{

    /**
     * Curl options array
     * @var array
     */
    private $options;

    /**
     * Curl request resource handle
     * @var resource
     */
    private $handle;

    /**
     * Information about a successfully completed request
     * @var array
     */
    private $info;

    /**
     * Curl error number
     * @var Integer
     */
    private $errno;

    /**
     * Curl error message
     * @var String
     */
    private $errmsg;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $options = array();

        // default: no error
        $this->errno  = 0;
        $this->errmsg = '';

        // default: no info
        $this->info   = array();

        // set default curl options
        $this->options[CURLOPT_TIMEOUT]        = 30;
        $this->options[CURLOPT_RETURNTRANSFER] = TRUE;
        $this->options[CURLOPT_FOLLOWLOCATION] = TRUE;
        $this->options[CURLOPT_FAILONERROR]    = TRUE;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->options);
        unset($this->errno);
        unset($this->errmsg);
        unset($this->info);
    }

    /**
     * Get access to certain private attributes.
     *
     * This gives access to errno, errmsg and info.
     *
     * @param String $name Attribute name
     *
     * @return mixed $return Value of the chosen attribute
     */
    public function __get($name)
    {
        switch ($name)
        {
            case "errno":
            case "errmsg":
            case "info":
                return $this->{$name};
                break;
        }
    }

    /**
     * Set multiple curl config options at once.
     *
     * @param array $options Array of curl config options
     *
     * @return Boolean $return TRUE if it was stored successfully
     *                         FALSE if the input is not an array
     */
    public function set_options($options)
    {
        if (!is_array($options))
        {
            return FALSE;
        }

        $this->options = $options + $this->options;

        return TRUE;
    }

    /**
     * Set a curl config option.
     *
     * @param String $key   Name of a curl config key (minus 'CURLOPT_')
     * @param mixed  $value Value of that config options
     *
     * @return void
     */
    public function set_option($key, $value)
    {
        if (is_string($key) && !is_numeric($key))
        {
            $key = constant('CURLOPT_' . strtoupper($key));
        }

        $this->options[$key] = $value;
    }

    /**
     * Initialize the curl request.
     *
     * @param String $url URL for the request
     *
     * @return Boolean $return TRUE if the initialization was successful,
     *                         FALSE otherwise
     */
    private function init($url)
    {
        $this->handle = curl_init($url);

        if (!curl_setopt_array($this->handle, $this->options))
        {
            $this->errmsg = "Could not set curl options!";
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Execute a curl request.
     *
     * @return mixed $return Return value
     */
    private function execute()
    {
        $return = curl_exec($this->handle);

        if ($return === FALSE)
        {
            $this->errno  = curl_errno($this->handle);
            $this->errmsg = curl_error($this->handle);

            curl_close($this->handle);
            $this->handle = NULL;

            return $return;
        }
        else
        {
            $this->info = curl_getinfo($this->handle);

            curl_close($this->handle);
            $this->handle = NULL;

            return $return;
        }
    }

    /**
     * Retrieve remote content.
     *
     * @param String $location Remote location
     *
     * @return mixed $return Return value
     */
    public function simple_get($location)
    {
        if ($this->init($location) === FALSE)
        {
            return FALSE;
        }

        return $this->execute();
    }

    /**
     * Post data to a remote service.
     *
     * @param String $location Remote service
     * @param mixed  $data     Data to post
     *
     * @return mixed $return Return value
     */
    public function simple_post($location, $data)
    {
        if ($this->init($location) === FALSE)
        {
            return FALSE;
        }

        $this->options[CURLOPT_CUSTOMREQUEST] = "POST";
        $this->options[CURLOPT_POST]          = TRUE;
        $this->options[CURLOPT_POSTFIELDS]    = $data;

        return $this->execute();
    }

}

?>