<?php

/**
 * This file contains the BadRequestExceptionBaseTest class.
 *
 * PHP Version 7.0
 *
 * @package Lunr\Corona\Exceptions
 * @author  Heinz Wiesinger <heinz@m2mobi.com>
 */

namespace Lunr\Corona\Exceptions\Tests;

use Lunr\Corona\Exceptions\Tests\Helpers\HttpExceptionTest;
use Exception;

/**
 * This class contains tests for the BadRequestException class.
 *
 * @covers Lunr\Corona\Exceptions\BadRequestException
 */
class BadRequestExceptionBaseTest extends HttpExceptionTest
{

    /**
     * Test that the error code was set correctly.
     */
    public function testErrorCodeSetCorrectly()
    {
        $this->assertPropertySame('code', 400);
    }

    /**
     * Test that the error code was set correctly.
     */
    public function testApplicationErrorCodeSetCorrectly()
    {
        $this->assertPropertySame('app_code', $this->code);
    }

    /**
     * Test that the error message was passed correctly.
     */
    public function testErrorMessagePassedCorrectly()
    {
        $this->expectExceptionMessage($this->message);

        throw $this->class;
    }

}

?>
