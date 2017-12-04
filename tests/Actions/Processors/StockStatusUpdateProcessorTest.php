<?php

/**
 * TechDivision\Import\Product\Actions\Processors\StockStatusUpdateProcessorTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Actions\Processors;

use TechDivision\Import\Product\Repositories\SqlStatementRepository;

/**
 * Test class for the stock status update processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class StockStatusUpdateProcessorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The processor impelementation we want to test.
     *
     * @var \TechDivision\Import\Product\Actions\Processors\StockStatusUpdateProcessor
     */
    protected $processor;

    /**
     * A mock connection implementation.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockConnection;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // mock the \PDO connnection
        $this->mockConnection = $this->getMockBuilder('TechDivision\Import\Connection\ConnectionInterface')
                                     ->setMethods(get_class_methods('TechDivision\Import\Connection\ConnectionInterface'))
                                     ->getMock();

        // initialize the processor we want to test
        $this->processor = new StockStatusUpdateProcessor($this->mockConnection, new SqlStatementRepository());
    }

    /**
     * Test's the execute() method successfull.
     *
     * @return void
     */
    public function testExecuteWithUpdate()
    {

        // prepare the expected SQL statement
        $expectedStatement = 'UPDATE cataloginventory_stock_status SET website_id=:website_id,stock_id=:stock_id,qty=:qty,stock_status=:stock_status WHERE product_id=:product_id AND website_id=:website_id AND stock_id=:stock_id';

        // prepare the row with the data that has to be updated
        $row = array(
            'product_id' => '489865',
            'website_id' => 0,
            'stock_id' => 1,
            'qty' => '0.0000',
            'stock_status' => 0,
            'techdivision_import_utils_entityStatus_memberName' => 'update'
        );

        // prepare the mock \PDOStatement
        $mockPdoStatement = $this->getMockBuilder('\PDOStatement')
                                 ->setMethods(get_class_methods('\PDOStatement'))
                                 ->getMock();

        // mock the \PDO::prepare() method
        $this->mockConnection->expects($this->once())
                             ->method('prepare')
                             ->with($expectedStatement)
                             ->willReturn($mockPdoStatement);

        // exectute the statement
        $this->processor->execute($row);
    }
}
