<?php

/**
 * This file contains the json view class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Corona;

use JsonSchema\Validator;
use Lunr\Core\Configuration;
use stdClass;
use Throwable;

/**
 * View class for displaying JSON return values.
 */
class JsonView extends View
{

    /**
     * Shared instance of the JSON validator
     * @var Validator
     */
    protected readonly Validator $json_validator;

    /**
     * The list of calls to check and what to check them against
     * @var array
     */
    protected array $allowlist = [];

    /**
     * Constructor.
     *
     * @param Request        $request       Shared instance of the Request class
     * @param Response       $response      Shared instance of the Response class
     * @param Configuration  $configuration Shared instance of the Configuration class
     * @param Validator|null $locator       Shared instance of the Validator class
     */
    public function __construct($request, $response, $configuration, $validator = NULL)
    {
        parent::__construct($request, $response, $configuration);

        if($validator !== NULL) {
            $this->json_validator = $validator;

            $this->configuration->load_file('validation');
            $this->allowlist = $this->configuration['validation']['schemalist']->toArray();
        }
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
    protected function prepare_data($data): mixed
    {
        return $data;
    }

    /**
     * Build the actual display and print it.
     *
     * @return void
     */
    public function print_page(): void
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

        if(in_array($code, $this->configuration['validation']['http_codes']->toArray())) {
            $this->get_json_validated($json);
        }


        if ($this->request->sapi == 'cli')
        {
            echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        }
        else
        {
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Build display for Fatal Error output.
     *
     * @return void
     */
    public function print_fatal_error(): void
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
    public function print_exception($e): void
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


    /**
     * Check if the JSON needs to be validated and do so if needed.
     *
     * @param array $json the response data passed by reference
     *
     * @return void
     */
    private function get_json_validated(array &$json): void
    {

        $schema_name = $this->request->controller . '/' . $this->request->method;
        if(!array_key_exists($schema_name, $this->allowlist))
        {
            return;
        }

        $schema_path = $this->request->application_path . str_replace($this->request->base_path, '', $this->statics($this->allowlist[$schema_name]));
        if(!is_readable($schema_path)) {
            $json['status']['json_message'] = 'JSON schema was not found, not validated';
            return;
        }

        $this->json_validator->validate($data, (object)[ '$ref' => 'file://' . realpath($schema_path)]);

        if (!$this->json_validator->isValid())
        {
            $json['status']['json_message'] = 'JSON failed to validate against the schema';
            $json['status']['json_code']    = HttpCode::INTERNAL_SERVER_ERROR;
        }
    }
}

?>
