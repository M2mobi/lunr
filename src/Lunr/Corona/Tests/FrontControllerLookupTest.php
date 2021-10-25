<?php

/**
 * This file contains the FrontControllerLookupTest class.
 *
 * @package    Lunr\Corona
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2015-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Corona\Tests;

/**
 * This class contains tests for getting controllers from the FrontController class.
 *
 * @covers Lunr\Corona\FrontController
 */
class FrontControllerLookupTest extends FrontControllerTest
{

    /**
     * Test that lookup() returns empty string with no identifier passed.
     *
     * @covers Lunr\Corona\FrontController::lookup
     */
    public function testVoidLookup(): void
    {
        $this->set_reflection_property_value('paths', []);

        $this->fao->expects($this->never())
                  ->method('find_matches');

        $value = $this->class->lookup();

        $this->assertEquals('', $value);
    }

    /**
     * Test lookup() with no paths set.
     *
     * @covers Lunr\Corona\FrontController::lookup
     */
    public function testLookupWithoutPath(): void
    {
        $this->set_reflection_property_value('paths', []);

        $this->fao->expects($this->never())
                  ->method('find_matches');

        $value = $this->class->lookup('test');

        $this->assertEquals('', $value);
    }

    /**
     * Test that lookup() finds controllers when looking in a single path.
     *
     * @covers Lunr\Corona\FrontController::lookup
     */
    public function testLookupWithSinglePath(): void
    {
        $this->set_reflection_property_value('paths', [ 'test' => '/foo/bar' ]);

        $dir    = '/foo/bar';
        $result = '/foo/bar/Project/Package/FunctionController.php';
        $fqcn   = 'Project\\Package\\FunctionController';

        $this->request->expects($this->exactly(2))
                      ->method('__get')
                      ->with('controller')
                      ->will($this->returnValue('function'));

        $this->fao->expects($this->once())
                  ->method('find_matches')
                  ->with('/^.+\/functioncontroller.php/i', $dir)
                  ->will($this->returnValue([ $result ]));

        $value = $this->class->lookup('test');

        $this->assertEquals($fqcn, $value);
    }

    /**
     * Test that lookup() finds controllers when looking in multiple paths.
     *
     * @covers Lunr\Corona\FrontController::lookup
     */
    public function testLookupWithMultiplePaths(): void
    {
        $this->set_reflection_property_value('paths', [ 'test' => '/foo/bar', 'live' => '/foo/baz', 'acc' => '/foo/bay' ]);

        $result = '/foo/baz/Project/Package/FunctionController.php';
        $fqcn   = 'Project\\Package\\FunctionController';

        $this->request->expects($this->exactly(4))
                      ->method('__get')
                      ->with('controller')
                      ->will($this->returnValue('function'));

        $this->fao->expects($this->exactly(2))
                  ->method('find_matches')
                  ->withConsecutive(
                      [ '/^.+\/functioncontroller.php/i', '/foo/bar' ],
                      [ '/^.+\/functioncontroller.php/i', '/foo/baz' ]
                  )
                  ->willReturnOnConsecutiveCalls([], [ $result ]);

        $value = $this->class->lookup('test', 'live');

        $this->assertEquals($fqcn, $value);
    }

    /**
     * Test lookup() with a nonexisting path.
     *
     * @covers Lunr\Corona\FrontController::lookup
     */
    public function testLookupWithNonExistingPath(): void
    {
        $this->set_reflection_property_value('paths', [ 'test' => '/foo/bar', 'live' => '/foo/baz', 'acc' => '/foo/bay' ]);

        $result = '/foo/baz/Project/Package/FunctionController.php';
        $fqcn   = 'Project\\Package\\FunctionController';

        $this->fao->expects($this->never())
                  ->method('find_matches');

        $value = $this->class->lookup('prod');

        $this->assertEquals('', $value);
    }

    /**
     * Test that lookup() finds controllers when looking in all paths.
     *
     * @covers Lunr\Corona\FrontController::lookup
     */
    public function testLookupWithAllPaths(): void
    {
        $this->set_reflection_property_value('paths', [ 'test' => '/foo/bar', 'live' => '/foo/baz' ]);

        $result = '/foo/baz/Project/Package/FunctionController.php';
        $fqcn   = 'Project\\Package\\FunctionController';

        $this->request->expects($this->exactly(4))
                      ->method('__get')
                      ->with('controller')
                      ->will($this->returnValue('function'));

        $this->fao->expects($this->exactly(2))
                  ->method('find_matches')
                  ->withConsecutive(
                      [ '/^.+\/functioncontroller.php/i', '/foo/bar' ],
                      [ '/^.+\/functioncontroller.php/i', '/foo/baz' ]
                  )
                  ->willReturnOnConsecutiveCalls([], [ $result ]);

        $value = $this->class->lookup();

        $this->assertEquals($fqcn, $value);
    }

    /**
     * Test that lookup() returns an empty string when no controller is found.
     *
     * @covers Lunr\Corona\FrontController::lookup
     */
    public function testLookupFails(): void
    {
        $this->set_reflection_property_value('paths', [ 'test' => '/foo/bar' ]);

        $dir    = '/foo/bar';
        $result = '/foo/bar/Project/Package/FunctionController.php';
        $fqcn   = 'Project\\Package\\FunctionController';

        $this->request->expects($this->exactly(2))
                      ->method('__get')
                      ->with('controller')
                      ->will($this->returnValue('function'));

        $this->fao->expects($this->once())
                  ->method('find_matches')
                  ->with('/^.+\/functioncontroller.php/i', $dir)
                  ->will($this->returnValue([]));

        $value = $this->class->lookup('test');

        $this->assertEquals('', $value);
    }

}
