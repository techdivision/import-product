<?php

/**
 * TechDivision\Import\Product\Observers\ClearProductObserverTest
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Observers;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Product\Utils\ColumnKeys;

/**
 * Test class for the observer implementation that clear's a product's URL rewrites.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ClearProductObserverTest extends TestCase
{

    /**
     * The observer we want to test.
     *
     * @var \TechDivision\Import\Product\Observers\ClearProductObserver
     */
    protected $observer;

    /**
     * A mock processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $mockProductBunchProcessor;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {

        // initialize a mock processor instance
        $this->mockProductBunchProcessor = $this->getMockBuilder('TechDivision\Import\Product\Services\ProductBunchProcessorInterface')
                                                ->setMethods(get_class_methods('TechDivision\Import\Product\Services\ProductBunchProcessorInterface'))
                                                ->getMock();

        // initialize the observer
        $this->observer = new ClearProductObserver($this->mockProductBunchProcessor);
    }

    /**
     * Test's the handle() method when the URL rewrites has successfully been removed.
     *
     * @return void
     */
    public function testHandle()
    {

        // create a dummy CSV file header
        $headers = array('sku' => 0 );

        // create a dummy CSV file row
        $row = array(0 => 'TEST-01');

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'hasHeader',
                                    'getHeader',
                                    'getLastSku',
                                    'getRow'
                                )
                            )
                            ->disableOriginalConstructor()
                            ->getMock();
        $mockSubject->expects($this->any())
                    ->method('hasHeader')
                    ->willReturn(true);
        $mockSubject->expects($this->any())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->any())
                    ->method('getHeader')
                    ->with(ColumnKeys::SKU)
                    ->willReturn(0);
        $mockSubject->expects($this->once())
                    ->method('getLastSku')
                    ->willReturn('TEST-02');

        // mock the processor methods
        $this->mockProductBunchProcessor->expects($this->once())
                                        ->method('deleteStockItem')
                                        ->with(array(ColumnKeys::SKU => $row[$headers[ColumnKeys::SKU]]));
        $this->mockProductBunchProcessor->expects($this->once())
                                        ->method('deleteProductWebsite')
                                        ->with(array(ColumnKeys::SKU => $row[$headers[ColumnKeys::SKU]]));
        $this->mockProductBunchProcessor->expects($this->once())
                                        ->method('deleteCategoryProduct')
                                        ->with(array(ColumnKeys::SKU => $row[$headers[ColumnKeys::SKU]]));
        $this->mockProductBunchProcessor->expects($this->once())
                                        ->method('deleteProduct')
                                        ->with(array(ColumnKeys::SKU => $row[$headers[ColumnKeys::SKU]]));

        // invoke the handle() method
        $this->assertSame($row, $this->observer->handle($mockSubject));
    }

    /**
     * Test's the handle() method with the SKU same as parent.
     *
     * @return void
     */
    public function testHandleWithParentSku()
    {

        // create a dummy CSV file header
        $row = array(
            0 => 'TEST-01'
        );

        // mock the loadProduct() method
        $this->mockProductBunchProcessor->expects($this->never())
                                        ->method('loadProduct');

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'hasHeader',
                                    'getHeader',
                                    'getHeaders',
                                    'getLastSku',
                                    'getRow',
                                    'preLoadEntityId'
                                )
                            )
                            ->disableOriginalConstructor()
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('getLastSku')
                    ->willReturn('TEST-01');
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('hasHeader')
                    ->willReturn(true);
        $mockSubject->expects($this->once())
                    ->method('getHeader')
                    ->with(ColumnKeys::SKU)
                    ->willReturn(0);

        // invoke the handle() method
        $this->assertSame($row, $this->observer->handle($mockSubject));
    }
}
