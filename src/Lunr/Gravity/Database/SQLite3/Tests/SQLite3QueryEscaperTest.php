<?php

/**
 * This file contains the SQLite3QueryEscaperTest class.
 *
 * @package    Lunr\Gravity\Database\SQLite3
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2012-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Gravity\Database\SQLite3\Tests;

use Lunr\Gravity\Database\SQLite3\SQLite3QueryEscaper;
use Lunr\Halo\LunrBaseTest;
use ReflectionClass;

/**
 * This class contains the tests for the SQLite3QueryEscaper class.
 *
 * @covers Lunr\Gravity\Database\SQLite3QueryEscaper
 */
abstract class SQLite3QueryEscaperTest extends LunrBaseTest
{

    /**
     * Mock instance of a class implementing the DatabaseStringEscaperInterface.
     * @var DatabaseStringEscaperInterface
     */
    protected $escaper;

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        $this->escaper = $this->getMockBuilder('Lunr\Gravity\Database\DatabaseStringEscaperInterface')
                              ->getMock();

        $this->class = new SQLite3QueryEscaper($this->escaper);

        $this->reflection = new ReflectionClass('Lunr\Gravity\Database\SQLite3\SQLite3QueryEscaper');
    }

    /**
     * Testcase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->escaper);
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Unit Test Data Provider for invalid indices.
     *
     * @return array $indices Array of invalid indices
     */
    public function invalidIndicesProvider(): array
    {
        $indices   = [];
        $indices[] = [ NULL ];
        $indices[] = [ FALSE ];
        $indices[] = [ 'string' ];
        $indices[] = [ new \stdClass() ];
        $indices[] = [ [] ];

        return $indices;
    }

    /**
     * Unit Test Data Provider for valid Index Keywords.
     *
     * @return array $keywords Array of valid index keywords.
     */
    public function validIndexKeywordProvider(): array
    {
        $keywords   = [];
        $keywords[] = [ 'INDEXED BY' ];

        return $keywords;
    }

}

?>
