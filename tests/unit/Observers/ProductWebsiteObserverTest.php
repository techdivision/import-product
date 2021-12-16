<?php

/**
 * TechDivision\Import\Product\Observers\ProductWebsiteObserverTest
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
use TechDivision\Import\Product\Utils\ConfigurationKeys;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Test class for the product website observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductWebsiteObserverTest extends TestCase
{

    /**
     * The observer we want to test.
     *
     * @var \TechDivision\Import\Product\Observers\ProductWebsiteObserver
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

        // initialize the observer
        $this->observer = new ProductWebsiteObserver($mockProductBunchProcessor);
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
            ColumnKeys::SKU              => 0,
            ColumnKeys::PRODUCT_WEBSITES => 1
        );

        // create a dummy CSV file header
        $row = array(
            0 => 'TEST-01',
            1 => null
        );

        // mock the subject configuration
        $mockConfiguration = $this->getMockBuilder(SubjectConfigurationInterface::class)
            ->setMethods(get_class_methods(SubjectConfigurationInterface::class))
            ->getMock();
        $mockConfiguration->expects($this->once())
            ->method('hasParam')
            ->with(ConfigurationKeys::CLEAN_UP_WEBSITE_PRODUCT_RELATIONS)
            ->willReturn(false);

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'hasHeader',
                                    'getHeader',
                                    'getHeaders',
                                    'hasBeenProcessed',
                                    'getLastEntityId',
                                    'getRow',
                                    'getConfiguration'
                                )
                            )
                            ->disableOriginalConstructor()
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('getConfiguration')
                    ->willReturn($mockConfiguration);
        $mockSubject->expects($this->any())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->any())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->any())
                    ->method('hasHeader')
                    ->willReturn(true);
        $mockSubject->expects($this->any())
                    ->method('getHeader')
                    ->withConsecutive(
                        array(ColumnKeys::SKU),
                        array(ColumnKeys::PRODUCT_WEBSITES)
                    )
                    ->willReturnOnConsecutiveCalls(0, 1);
        $mockSubject->expects($this->once())
                    ->method('hasBeenProcessed')
                    ->willReturn(false);
        $mockSubject->expects($this->never())
                    ->method('getLastEntityId');

        // invoke the handle() method
        $this->assertSame($row, $this->observer->handle($mockSubject));
    }
}
