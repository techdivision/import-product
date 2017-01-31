<?php

/**
 * TechDivision\Import\Product\Observers\ProductUpdateObserverTest
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
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\ColumnKeys;

/**
 * Test class for the product update observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductUpdateObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The observer we want to test.
     *
     * @var \TechDivision\Import\Product\Observers\PreImport\ProductUpdateObserver
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
        $this->observer = new ProductUpdateObserver();
    }

    /**
     * Test's the initializeProduct() method without an existing product.
     *
     * @return void
     */
    public function testHandleWithExistingProduct()
    {

        // create a dummy CSV file header
        $headers = array(
            'sku'                => 0,
            'created_at'         => 1,
            'updated_at'         => 2,
            'has_options'        => 3,
            'required_options'   => 4,
            'product_type'       => 5,
            'attribute_set_code' => 6
        );

        // create a dummy CSV file row
        $row = array(
            0 => $sku = '24-MB01',
            1 => '10/23/16, 5:10 PM',
            2 => '10/23/16, 5:10 PM',
            3 => 1,
            4 => 1,
            5 => $productType = 'simple',
            6 => $attributeSetCode = 'Bag'
        );

        // the old product data
        $oldProduct = array(
            'sku'              => $sku,
            'created_at'       => '2016-10-23 12:00:00',
            'updated_at'       => '2016-10-23 12:00:00',
            'has_options'      => 0,
            'required_options' => 0,
            'type_id'          => $productType,
            'attribute_set_id' => $attributeSetId = 15
        );

        // the new product data
        $newProduct = array(
            'sku'                     => $sku,
            'created_at'              => '2016-10-23 17:10:00',
            'updated_at'              => '2016-10-23 17:10:00',
            'has_options'             => 0,
            'required_options'        => 0,
            'type_id'                 => $productType,
            'attribute_set_id'        => $attributeSetId,
            EntityStatus::MEMBER_NAME => EntityStatus::STATUS_UPDATE
        );

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'hasHeader',
                                    'getHeader',
                                    'getHeaders',
                                    'hasBeenProcessed',
                                    'getAttributeSetByAttributeSetName',
                                    'getSourceDateFormat',
                                    'loadProduct',
                                    'persistProduct'
                                )
                            )
                            ->getMock();
        $mockSubject->expects($this->any())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->any())
                    ->method('hasHeader')
                    ->willReturn(true);
        $mockSubject->expects($this->any())
                    ->method('getHeader')
                    ->withConsecutive(
                        array(ColumnKeys::SKU),
                        array(ColumnKeys::CREATED_AT),
                        array(ColumnKeys::UPDATED_AT),
                        array(ColumnKeys::ATTRIBUTE_SET_CODE),
                        array(ColumnKeys::SKU),
                        array(ColumnKeys::PRODUCT_TYPE)
                     )
                    ->willReturnOnConsecutiveCalls(0, 1, 2, 6, 0, 5);
        $mockSubject->expects($this->once())
                    ->method('hasBeenProcessed')
                    ->willReturn(false);
        $mockSubject->expects($this->any(2))
                    ->method('getSourceDateFormat')
                    ->willReturn('n/d/y, g:i A');
        $mockSubject->expects($this->once())
                    ->method('getAttributeSetByAttributeSetName')
                    ->with($attributeSetCode)
                    ->willReturn(
                        array(
                            MemberNames::ATTRIBUTE_SET_ID   => 15,
                            MemberNames::ATTRIBUTE_SET_NAME => $attributeSetCode
                        )
                    );
        $mockSubject->expects($this->once())
                    ->method('loadProduct')
                    ->with($sku)
                    ->willReturn($oldProduct);
        $mockSubject->expects($this->once())
                    ->method('persistProduct')
                    ->with($newProduct);

        // inject the subject
        $this->observer->setSubject($mockSubject);

        // query whether or not the result is as expected
        $this->assertEquals($row, $this->observer->handle($row));
    }
}
