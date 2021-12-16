<?php

/**
 * TechDivision\Import\Product\Observers\CategoryProductObserverTest
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
use TechDivision\Import\Observers\AttributeLoaderInterface;
use TechDivision\Import\Observers\EntityMergers\EntityMergerInterface;

/**
 * Test class for the product category observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class CategoryProductObserverTest extends TestCase
{

    /**
     * The observer we want to test.
     *
     * @var \TechDivision\Import\Product\Observers\CategoryProductObserver
     */
    protected $observer;

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
        $mockProductBunchProcessor = $this->getMockBuilder('TechDivision\Import\Product\Services\ProductBunchProcessorInterface')
                                          ->setMethods(get_class_methods('TechDivision\Import\Product\Services\ProductBunchProcessorInterface'))
                                          ->getMock();

        // initialize the mock attribute loader instance
        $mockAttributeLoader = $this->getMockBuilder(AttributeLoaderInterface::class)->getMock();

        // initialize the mock entity merger instance
        $mockEntityMerger = $this->getMockBuilder(EntityMergerInterface::class)->getMock();

        // initialize the observer
        $this->observer = new CategoryProductObserver($mockProductBunchProcessor, $mockAttributeLoader, $mockEntityMerger);
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
            ColumnKeys::SKU             => 0,
            ColumnKeys::CATEGORIES      => 1
        );

        // create a dummy CSV file header
        $row = array(
            0 => 'TEST-01',
            1 => null
        );

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'hasHeader',
                                    'getHeader',
                                    'getHeaders',
                                    'hasBeenProcessed',
                                    'getRow'
                                )
                            )
                            ->disableOriginalConstructor()
                            ->getMock();
        $mockSubject->expects($this->any())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->any())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->any())
                    ->method('hasHeader')
                    ->willReturnOnConsecutiveCalls(true);
        $mockSubject->expects($this->any())
                    ->method('getHeader')
                    ->withConsecutive(array(ColumnKeys::SKU), array(ColumnKeys::CATEGORIES))
                    ->willReturnOnConsecutiveCalls(0, 1);
        $mockSubject->expects($this->once())
                    ->method('hasBeenProcessed')
                    ->willReturn('TEST-02');

        // invoke the handle() method
        $this->assertSame($row, $this->observer->handle($mockSubject));
    }
}
