<?php

/**
 * TechDivision\Import\Product\Observers\ClearProductObserverTest
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

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Product\Utils\ColumnKeys;

/**
 * Test class for the observer implementation that clear's a product's URL rewrites.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ClearProductObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The observer we want to test.
     *
     * @var \TechDivision\Import\Product\Observers\PreImport\ClearProductObserver
     */
    protected $observer;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->observer = new ClearProductObserver();
    }

    /**
     * Test's the handle() method when the URL rewrites has successfully been removed.
     *
     * @return void
     */
    public function testHandleWithSuccess()
    {

        // create a dummy CSV file header
        $headers = array('sku' => 0 );

        // create a dummy CSV file row
        $row = array(0 => 'TEST-01');

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'getHeaders',
                                    'getLastSku',
                                    'removeUrlRewrite',
                                    'removeStockItem',
                                    'removeStockStatus',
                                    'removeProductWebsite',
                                    'removeProductCategory',
                                    'removeProduct'
                                )
                            )
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->once())
                    ->method('getLastSku')
                    ->willReturn('TEST-02');
        $mockSubject->expects($this->once())
                    ->method('removeUrlRewrite')
                    ->with(array($row[$headers[ColumnKeys::SKU]]));
        $mockSubject->expects($this->once())
                    ->method('removeStockItem')
                    ->with(array($row[$headers[ColumnKeys::SKU]]));
        $mockSubject->expects($this->once())
                    ->method('removeStockStatus')
                    ->with(array($row[$headers[ColumnKeys::SKU]]));
        $mockSubject->expects($this->once())
                    ->method('removeProductWebsite')
                    ->with(array($row[$headers[ColumnKeys::SKU]]));
        $mockSubject->expects($this->once())
                    ->method('removeProductCategory')
                    ->with(array($row[$headers[ColumnKeys::SKU]]));
        $mockSubject->expects($this->once())
                    ->method('removeProduct')
                    ->with(array($row[$headers[ColumnKeys::SKU]]));

        // inject the subject und invoke the handle() method
        $this->observer->setSubject($mockSubject);
        $this->assertSame($row, $this->observer->handle($row));
    }
}
