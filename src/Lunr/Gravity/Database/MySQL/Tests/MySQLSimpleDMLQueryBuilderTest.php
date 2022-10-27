<?php

/**
 * This file contains the MySQLSimpleDMLQueryBuilderTest class.
 *
 * @package    Lunr\Gravity\Database\MySQL
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2013-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Gravity\Database\MySQL\Tests;

use Lunr\Gravity\Database\MySQL\MySQLSimpleDMLQueryBuilder;
use Lunr\Halo\LunrBaseTest;
use ReflectionClass;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the MySQLSimpleDMLQueryBuilder class.
 *
 * @covers Lunr\Gravity\Database\MySQL\MySQLSimpleDMLQueryBuilder
 */
abstract class MySQLSimpleDMLQueryBuilderTest extends LunrBaseTest
{

    /**
     * Mock instance of the MySQLQueryEscaper class.
     * @var MySQLQueryEscaper
     */
    protected $escaper;

    /**
     * Mock instance of the MySQLDMLQueryBuilder class.
     * @var MySQLDMLQueryBuilder
     */
    protected $builder;

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        $this->escaper = $this->getMockBuilder('Lunr\Gravity\Database\MySQL\MySQLQueryEscaper')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->builder = $this->getMockBuilder('Lunr\Gravity\Database\MySQL\MySQLDMLQueryBuilder')
                              ->getMock();

        $this->class      = new MySQLSimpleDMLQueryBuilder($this->builder, $this->escaper);
        $this->reflection = new ReflectionClass('Lunr\Gravity\Database\MySQL\MySQLSimpleDMLQueryBuilder');
    }

    /**
     * Testcase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->escaper);
        unset($this->class);
        unset($this->querybuilder);
        unset($this->reflection);
    }

    /**
     * Unit test data provider for location references.
     *
     * @return array $values Array of location reference values.
     */
    public function locationReferenceAliasProvider(): array
    {
        $values   = [];
        $values[] = [ 'table AS t', TRUE, 'table', 't', 'table AS t' ];
        $values[] = [ 'table as t', TRUE, 'table', 't', 'table AS t' ];
        $values[] = [ 'column AS c', FALSE, 'column', 'c', 'column AS c' ];
        $values[] = [ 'column as c', FALSE, 'column', 'c', 'column AS c' ];

        return $values;
    }

    /**
     * Unit test data provider for location references.
     *
     * @return array $values Array of location reference values.
     */
    public function locationReferenceProvider(): array
    {
        $values   = [];
        $values[] = [ 'table', TRUE, 'table' ];
        $values[] = [ 'column', FALSE, 'column' ];

        return $values;
    }

    /**
    * Unit test data provider for tested union operators.
    *
    * @return array $compound operators for union query
    */
    public function unionOperatorProvider(): array
    {
        $operators   = [];
        $operators[] = [ '' ];
        $operators[] = [ 'ALL' ];
        $operators[] = [ 'DISTINCT' ];
        $operators[] = [ TRUE ];
        $operators[] = [ FALSE ];

        return $operators;
    }

}

?>
