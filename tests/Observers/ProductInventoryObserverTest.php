<?php

/**
 * TechDivision\Import\Product\Observers\ProductInventoryObserverTest
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

use TechDivision\Import\Utils\EntityStatus;

/**
 * Test class for the product inventory observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductInventoryObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The observer we want to test.
     *
     * @var \TechDivision\Import\Product\Observers\ProductInventoryObserver
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
        $this->observer = new ProductInventoryObserver();
    }

    /**
     * Test's the handle() method with an empty value.
     *
     * @return void
     */
    public function testHandleWithEmptyValue()
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
            1  => '',
            2  => '',
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

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'hasBeenProcessed',
                                    'getLastEntityId',
                                    'persistStockItem',
                                    'persistStockStatus',
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
        $mockSubject->expects($this->once())
                    ->method('persistStockItem')
                    ->with(
                        array(
                            'product_id'                  => $lastEntityId,
                            'website_id'                  => 0,
                            'stock_id'                    => 1,
                            'qty'                         => 0.0,
                            'min_qty'                     => 0.0,
                            'use_config_min_qty'          => 0,
                            'is_qty_decimal'              => 0,
                            'backorders'                  => 0,
                            'use_config_backorders'       => 0,
                            'min_sale_qty'                => 0.0,
                            'use_config_min_sale_qty'     => 0,
                            'max_sale_qty'                => 0.0,
                            'use_config_max_sale_qty'     => 0,
                            'is_in_stock'                 => 0,
                            'notify_stock_qty'            => 0.0,
                            'use_config_notify_stock_qty' => 0,
                            'manage_stock'                => 0,
                            'use_config_manage_stock'     => 0,
                            'use_config_qty_increments'   => 0,
                            'qty_increments'              => 0.0,
                            'use_config_enable_qty_inc'   => 0,
                            'enable_qty_increments'       => 0,
                            'is_decimal_divided'          => 0,
                            EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                        )
                    );
        $mockSubject->expects($this->once())
                    ->method('persistStockStatus')
                    ->with(
                        array(
                            'product_id'              => $lastEntityId,
                            'website_id'              => 0,
                            'stock_id'                => 1,
                            'stock_status'            => 0,
                            'qty'                     => 0.000,
                            EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE
                        )
                    );

        // invoke the handle() method
        $this->assertSame($row, $this->observer->handle($mockSubject));
    }
}
