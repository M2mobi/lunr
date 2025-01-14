<?php

/**
 * This file contains the json view class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Corona;

use Lunr\Core\Configuration;
use stdClass;
use Throwable;
use Lunr\Corona\Exceptions\InternalServerErrorException;

/**
 * View class for displaying JSON return values.
 */
class JsonView extends View
{

    /**
     * Constructor.
     *
     * @param Request       $request       Shared instance of the Request class
     * @param Response      $response      Shared instance of the Response class
     * @param Configuration $configuration Shared instance of the Configuration class
     */
    public function __construct($request, $response, $configuration)
    {
        parent::__construct($request, $response, $configuration);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Prepare the response data before using it for generating the output.
     *
     * @param mixed $data Response data to prepare
     *
     * @return mixed $return Prepared response data
     */
    protected function prepare_data($data)
    {
        return $data;
    }

    /**
     * Build the actual display and print it.
     *
     * @return void
     */
    public function print_page()
    {
        $identifier = $this->response->get_return_code_identifiers(TRUE);

        $info = $this->response->get_error_info($identifier);
        $msg  = $this->response->get_error_message($identifier);
        $code = $this->response->get_return_code($identifier);

        $json = [];

        $json['data']   = $this->prepare_data($this->response->get_response_data());
        $json['status'] = [];

        $json['status']['code']    = !is_numeric($info) || is_float($info + 1) ? $code : $info;
        $json['status']['message'] = is_null($msg) ? '' : $msg;

        if ($json['data'] === [])
        {
            $json['data'] = new stdClass();
        }

        header('Content-type: application/json');
        http_response_code($code);

        $return = NULL;
        if ($this->request->sapi == 'cli')
        {
            $return = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        }
        else
        {
            $return = json_encode($json, JSON_UNESCAPED_UNICODE);
        }

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            throw new InternalServerErrorException('JSON encoding failed: ' . json_last_error_msg());
        }

        echo $return;
    }

    /**
     * Build display for Fatal Error output.
     *
     * @return void
     */
    public function print_fatal_error()
    {
        $error = error_get_last();

        if ($this->is_fatal_error($error) === FALSE)
        {
            return;
        }

        $json = [];

        $json['data']   = [];
        $json['status'] = [];

        $json['status']['code']    = 500;
        $json['status']['message'] = $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line'];

        header('Content-type: application/json');
        http_response_code(500);

        if ($this->request->sapi == 'cli')
        {
            echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT) . "\n";
        }
        else
        {
            echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        }
    }

    /**
     * Build display for uncaught exception output.
     *
     * @param Throwable $e Uncaught exception
     *
     * @return void
     */
    public function print_exception($e)
    {
        $json = [];

        $json['data']   = [];
        $json['status'] = [];

        $json['status']['code']    = 500;
        $json['status']['message'] = sprintf(
            'Uncaught Exception %s: "%s" at %s line %s',
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
        );

        header('Content-type: application/json');
        http_response_code(500);

        if ($this->request->sapi == 'cli')
        {
            echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT) . "\n";
        }
        else
        {
            echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        }
    }

}

?>
