<?php

/**
 * TechDivision\Import\Product\Observers\ProductInventoryObserverTest
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
use TechDivision\Import\Dbal\Utils\EntityStatus;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Observers\DynamicAttributeLoader;
use TechDivision\Import\Subjects\I18n\NumberConverterInterface;

/**
 * Test class for the product inventory observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductInventoryObserverTest extends TestCase
{

    /**
     * The observer we want to test.
     *
     * @var \TechDivision\Import\Product\Observers\ProductInventoryObserver
     */
    protected $observer;

    /**
     * A mock processor instance.
     *
     * @var \PHPUnit\Framework\MockObject\MockObject
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

        // mock the methods
        $this->mockProductBunchProcessor->expects($this->any())->method('loadRawEntity')->willReturnArgument(1);

        // initialize the observer
        $this->observer = new ProductInventoryObserver($this->mockProductBunchProcessor, new DynamicAttributeLoader());
    }

    /**
     * Test's the handle() method with an empty value.
     *
     * @return void
     */
    public function testHandleWithEmptyStockValue()
    {

        // create a dummy CSV file row
        $headers = array(
            'sku'                         => 0,
            'website_id'                  => 1,
            'qty'                         => 2,
            'out_of_stock_qty'            => 3,
            'use_config_min_qty'          => 4,
            'is_qty_decimal'              => 5,
            'allow_backorders'            => 6,
            'use_config_backorders'       => 7,
            'min_cart_qty'                => 8,
            'use_config_min_sale_qty'     => 9,
            'max_cart_qty'                => 10,
            'use_config_max_sale_qty'     => 11,
            'is_in_stock'                 => 12,
            'notify_on_stock_below'       => 13,
            'use_config_notify_stock_qty' => 14,
            'manage_stock'                => 15,
            'use_config_manage_stock'     => 16,
            'use_config_qty_increments'   => 17,
            'qty_increments'              => 18,
            'use_config_enable_qty_inc'   => 19,
            'enable_qty_increments'       => 20,
            'is_decimal_divided'          => 21
        );

        // create a dummy CSV file header
        $row = array(
            0  => 'TEST-01',
            1  => '1',
            2  => '100',
            4  => '',
            5  => '',
            6  => '',
            7  => '',
            8  => '',
            9  => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '',
            14 => '',
            15 => '',
            16 => '',
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => ''
        );

        // initialize the number converter
        $mockNumberConverter = $this->getMockBuilder(NumberConverterInterface::class)
            ->setMethods(get_class_methods(NumberConverterInterface::class))
            ->getMock();
        $mockNumberConverter
            ->expects($this->any())
            ->method('parse')
            ->with(100)
            ->willReturn(100.00);

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'hasBeenProcessed',
                                    'getLastEntityId',
                                    'getNumberConverter',
                                    'getRow'
                                )
                            )
                            ->disableOriginalConstructor()
                            ->getMock();
        $mockSubject->setHeaders($headers);
        $mockSubject->expects($this->once())
                    ->method('hasBeenProcessed')
                    ->willReturn(false);
        $mockSubject->expects($this->any())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->exactly(2))
                    ->method('getLastEntityId')
                    ->willReturn($lastEntityId = 12345);
        $mockSubject->expects($this->any())
                    ->method('getNumberConverter')
                    ->willReturn($mockNumberConverter);

        // mock the processor methods
        $this->mockProductBunchProcessor->expects($this->once())
                                        ->method('persistStockItem')
                                        ->with(
                                            array(
                                                'product_id'              => $lastEntityId,
                                                'website_id'              => 1,
                                                'stock_id'                => 1,
                                                'qty'                     => 100,
                                                EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE
                                            )
                                        );

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
                                    'hasBeenProcessed',
                                    'getRow',
                                    'preLoadEntityId'
                                )
                            )
                            ->disableOriginalConstructor()
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('hasBeenProcessed')
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
