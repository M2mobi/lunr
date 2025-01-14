<?php

/**
 * This file contains the JsonViewPrintTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Corona\Tests;

use stdClass;

/**
 * This class contains tests for the JsonView class.
 *
 * @covers Lunr\Corona\JsonView
 */
class JsonViewPrintTest extends JsonViewTest
{

    /**
     * JSON return value;
     * @var array
     */
    private $json;

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->json = [ 'a' => 100, 'b' => [ 'z' => TRUE ], 'c' => [ NULL ], 'd' => new stdClass(), 'e' => 'みんな' ];
    }

    /**
     * Test that print_page() prints JSON with the response code as error info.
     *
     * @param mixed $error_info Non-integer error info value
     *
     * @dataProvider invalidErrorInfoProvider
     * @requires     PHP 5.5.12
     * @covers       Lunr\Corona\JsonView::print_page
     */
    public function testPrintPagePrintsJsonWithCode($error_info): void
    {
        $this->response->expects($this->once())
                       ->method('get_return_code_identifiers')
                       ->with(TRUE)
                       ->willReturn('id');

        $this->response->expects($this->once())
                       ->method('get_error_info')
                       ->with('id')
                       ->willReturn(NULL);

        $this->response->expects($this->once())
                       ->method('get_error_message')
                       ->with('id')
                       ->willReturn('Message');

        $this->response->expects($this->once())
                       ->method('get_return_code')
                       ->with('id')
                       ->willReturn(404);

        $this->response->expects($this->once())
                       ->method('get_response_data')
                       ->willReturn($this->json);

        $this->request->expects($this->once())
                      ->method('__get')
                      ->with('sapi')
                      ->willReturn('cli');

        $this->expectOutputMatchesFile(TEST_STATICS . '/Corona/json_code.json');

        $this->mock_function('header', function () {});
        $this->mock_function('http_response_code', function () {});

        $this->class->print_page();

        $this->unmock_function('header');
        $this->unmock_function('http_response_code');
    }

    /**
     * Test that print_page() prints JSON with an empty string as message if message is missing.
     *
     * @requires PHP 5.5.12
     * @covers   Lunr\Corona\JsonView::print_page
     */
    public function testPrintPagePrintsJsonWithoutMessage(): void
    {
        $this->response->expects($this->once())
                       ->method('get_return_code_identifiers')
                       ->with(TRUE)
                       ->willReturn('id');

        $this->response->expects($this->once())
                       ->method('get_error_info')
                       ->with('id')
                       ->willReturn(NULL);

        $this->response->expects($this->once())
                       ->method('get_error_message')
                       ->with('id')
                       ->willReturn(NULL);

        $this->response->expects($this->once())
                       ->method('get_return_code')
                       ->with('id')
                       ->willReturn(404);

        $this->response->expects($this->once())
                       ->method('get_response_data')
                       ->willReturn($this->json);

        $this->request->expects($this->once())
                      ->method('__get')
                      ->with('sapi')
                      ->willReturn('cli');

        $this->expectOutputMatchesFile(TEST_STATICS . '/Corona/json_empty_message.json');

        $this->mock_function('header', function () {});
        $this->mock_function('http_response_code', function () {});

        $this->class->print_page();

        $this->unmock_function('header');
        $this->unmock_function('http_response_code');
    }

    /**
     * Test that print_page() prints JSON with an empty string as message if message is missing.
     *
     * @runInSeparateProcess
     * @requires PHP 5.5.12
     * @covers   \Lunr\Corona\JsonView::print_page
     */
    public function testPrintPageThrowsOnEncodingFailure()
    {
        $this->response->expects($this->once())
                       ->method('get_return_code_identifiers')
                       ->with($this->equalTo(TRUE))
                       ->will($this->returnValue('id'));

        $this->response->expects($this->once())
                       ->method('get_error_info')
                       ->with($this->equalTo('id'))
                       ->will($this->returnValue(NULL));

        $this->response->expects($this->once())
                       ->method('get_error_message')
                       ->with($this->equalTo('id'))
                       ->will($this->returnValue(NULL));

        $this->response->expects($this->once())
                       ->method('get_return_code')
                       ->with($this->equalTo('id'))
                       ->will($this->returnValue(404));

        $this->response->expects($this->once())
                       ->method('get_response_data')
                       ->will($this->returnValue($this->json));

        $this->request->expects($this->once())
                      ->method('__get')
                      ->with($this->equalTo('sapi'))
                      ->will($this->returnValue('cli'));

        $this->expectException('Lunr\Corona\Exceptions\InternalServerErrorException');
        $this->expectExceptionMessage('JSON encoding failed: some error occurred');

        $this->mock_function('header', function() {});
        $this->mock_function('json_last_error', function() { return 1; });
        $this->mock_function('json_last_error_msg', function() { return 'some error occurred'; });

        $this->class->print_page();

        $this->unmock_function('json_last_error_msg');
        $this->unmock_function('json_last_error');
        $this->unmock_function('header');
    }

    /**
     * Test that print_page() prints JSON.
     *
     * @requires PHP 5.5.12
     * @covers   Lunr\Corona\JsonView::print_page
     */
    public function testPrintPagePrintsJson(): void
    {
        $this->response->expects($this->once())
                       ->method('get_return_code_identifiers')
                       ->with(TRUE)
                       ->willReturn('id');

        $this->response->expects($this->once())
                       ->method('get_error_info')
                       ->with('id')
                       ->willReturn(4040);

        $this->response->expects($this->once())
                       ->method('get_error_message')
                       ->with('id')
                       ->willReturn('Message');

        $this->response->expects($this->once())
                       ->method('get_return_code')
                       ->with('id')
                       ->willReturn(404);

        $this->response->expects($this->once())
                       ->method('get_response_data')
                       ->willReturn($this->json);

        $this->request->expects($this->once())
                      ->method('__get')
                      ->with('sapi')
                      ->willReturn('cli');

        $this->expectOutputMatchesFile(TEST_STATICS . '/Corona/json_complete.json');

        $this->mock_function('header', function () {});
        $this->mock_function('http_response_code', function () {});

        $this->class->print_page();

        $this->unmock_function('header');
        $this->unmock_function('http_response_code');
    }

    /**
     * Test that print_page() for a non-cli SAPI does not pretty print the output.
     *
     * @covers Lunr\Corona\JsonView::print_page
     */
    public function testPrintPageForWebDoesNotPrettyPrint(): void
    {
        $this->response->expects($this->once())
                       ->method('get_return_code_identifiers')
                       ->with(TRUE)
                       ->willReturn('id');

        $this->response->expects($this->once())
                       ->method('get_error_info')
                       ->with('id')
                       ->willReturn(4040);

        $this->response->expects($this->once())
                       ->method('get_error_message')
                       ->with('id')
                       ->willReturn('Message');

        $this->response->expects($this->once())
                       ->method('get_return_code')
                       ->with('id')
                       ->willReturn(404);

        $this->response->expects($this->once())
                       ->method('get_response_data')
                       ->willReturn($this->json);

        $this->request->expects($this->once())
                      ->method('__get')
                      ->with('sapi')
                      ->willReturn('web');

        $this->expectOutputString('{"data":{"a":100,"b":{"z":true},"c":[null],"d":{},"e":"みんな"},"status":{"code":4040,"message":"Message"}}');

        $this->mock_function('header', function () {});
        $this->mock_function('http_response_code', function () {});

        $this->class->print_page();

        $this->unmock_function('header');
        $this->unmock_function('http_response_code');
    }

    /**
     * Test that print_page() with empty data value.
     *
     * @requires PHP 5.5.12
     * @covers   Lunr\Corona\JsonView::print_page
     */
    public function testPrintPageWithEmptyData(): void
    {
        $this->response->expects($this->once())
                       ->method('get_return_code_identifiers')
                       ->with(TRUE)
                       ->willReturn('id');

        $this->response->expects($this->once())
                       ->method('get_error_info')
                       ->with('id')
                       ->willReturn(4040);

        $this->response->expects($this->once())
                       ->method('get_error_message')
                       ->with('id')
                       ->willReturn('Message');

        $this->response->expects($this->once())
                       ->method('get_return_code')
                       ->with('id')
                       ->willReturn(404);

        $this->response->expects($this->once())
                       ->method('get_response_data')
                       ->willReturn([]);

        $this->request->expects($this->once())
                      ->method('__get')
                      ->with('sapi')
                      ->willReturn('cli');

        $this->expectOutputMatchesFile(TEST_STATICS . '/Corona/json_empty_data.json');

        $this->mock_function('header', function () {});
        $this->mock_function('http_response_code', function () {});

        $this->class->print_page();

        $this->unmock_function('header');
        $this->unmock_function('http_response_code');
    }

    /**
     * Test that print_page() sets the proper JSON content type.
     *
     * @runInSeparateProcess
     *
     * @requires PHP 5.5.12
     * @requires extension xdebug
     * @covers   Lunr\Corona\JsonView::print_page
     */
    public function testPrintPageSetsContentType(): void
    {
        $this->response->expects($this->once())
                       ->method('get_return_code_identifiers')
                       ->with(TRUE)
                       ->willReturn('id');

        $this->response->expects($this->once())
                       ->method('get_error_info')
                       ->with('id')
                       ->willReturn(4040);

        $this->response->expects($this->once())
                       ->method('get_error_message')
                       ->with('id')
                       ->willReturn('Message');

        $this->response->expects($this->once())
                       ->method('get_return_code')
                       ->with('id')
                       ->willReturn(404);

        $this->response->expects($this->once())
                       ->method('get_response_data')
                       ->willReturn($this->json);

        $this->request->expects($this->once())
                      ->method('__get')
                      ->with('sapi')
                      ->willReturn('cli');

        $this->expectOutputMatchesFile(TEST_STATICS . '/Corona/json_complete.json');

        $this->class->print_page();

        $headers = xdebug_get_headers();

        $this->assertIsArray($headers);
        $this->assertNotEmpty($headers);

        $value = strpos($headers[0], 'X-Xdebug-Profile-Filename') !== FALSE ? $headers[2] : $headers[1];

        $this->assertEquals('Content-type: application/json', $value);
    }

}

?>
