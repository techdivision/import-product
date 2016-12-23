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
    public function testInitializeProductWithoutExistingProduct()
    {

        // initialize the expected result
        $expectedResult = array(
            'sku'              => $sku             = '24-MB01',
            'created_at'       => $createdAt       = '2016-10-24 12:36:00',
            'updated_at'       => $updatedAt       = '2016-10-24 12:37:00',
            'has_options'      => $hasOptions      = 1,
            'required_options' => $requiredOptions = 1,
            'type_id'          => $typeId          = 'simple',
            'attribute_set_id' => $attributeSetId  = 15
        );

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
            ->setMethods(array('loadProduct'))
            ->getMock();
        $mockSubject->expects($this->once())
            ->method('loadProduct')
            ->with($sku)
            ->willReturn(null);

        // inject the subject
        $this->observer->setSubject($mockSubject);

        // initialize the product
        $result = $this->observer->initializeProduct(
            $sku,
            $createdAt,
            $updatedAt,
            $hasOptions,
            $requiredOptions,
            $typeId,
            $attributeSetId
        );

        // query whether or not the result is as expected
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test's the initializeProduct() method with existing product.
     *
     * @return void
     */
    public function testInitializeProductWithExistingProduct()
    {

        // initialize the expected result
        $expectedResult = array(
            'entity_id'        => $entityId        = 92000,
            'sku'              => $sku             = '24-MB01',
            'created_at'       => $createdAt       = '2016-10-24 12:36:00',
            'updated_at'       => $updatedAt       = '2016-10-24 12:37:00',
            'has_options'      => $hasOptions      = 1,
            'required_options' => $requiredOptions = 1,
            'type_id'          => $typeId          = 'simple',
            'attribute_set_id' => $attributeSetId  = 15
        );

        // the product that has to be loaded
        $product = array(
            'entity_id'        => $entityId,
            'sku'              => $sku,
            'created_at'       => '2016-10-24 12:36:00',
            'updated_at'       => '2016-10-24 12:36:00',
            'has_options'      => 0,
            'required_options' => 0,
            'type_id'          => $typeId,
            'attribute_set_id' => $attributeSetId
        );

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
            ->setMethods(array('loadProduct'))
            ->getMock();
        $mockSubject->expects($this->once())
            ->method('loadProduct')
            ->with($sku)
            ->willReturn($product);

        // inject the subject
        $this->observer->setSubject($mockSubject);

        // initialize the product
        $result = $this->observer->initializeProduct(
            $sku,
            $createdAt,
            $updatedAt,
            $hasOptions,
            $requiredOptions,
            $typeId,
            $attributeSetId
        );

        // query whether or not the result is as expected
        $this->assertEquals($expectedResult, $result);
    }
}
