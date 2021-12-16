<?php

/**
 * TechDivision\Import\Product\Observers\ProductObserverTest
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
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\ConfigurationKeys;
use TechDivision\Import\Subjects\I18n\DateConverterInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Test class for the product update observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductObserverTest extends TestCase
{

    /**
     * The observer we want to test.
     *
     * @var \TechDivision\Import\Product\Observers\ProductObserver
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
        $this->observer = new ProductObserver($this->mockProductBunchProcessor);
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
            'entity_id'        => 1234,
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
            'entity_id'               => 1234,
            'sku'                     => $sku,
            'created_at'              => '2016-10-23 12:00:00',
            'updated_at'              => '2016-10-23 17:10:00',
            'has_options'             => 0,
            'required_options'        => 0,
            'type_id'                 => $productType,
            'attribute_set_id'        => $attributeSetId,
            EntityStatus::MEMBER_NAME => EntityStatus::STATUS_UPDATE
        );

        // mock the date converter
        $mockDateConverter = $this->getMockBuilder(DateConverterInterface::class)->getMock();
        $mockDateConverter->expects($this->exactly(2))
            ->method('convert')
            ->withConsecutive(array('10/23/16, 5:10 PM'), array('10/23/16, 5:10 PM'))
            ->willReturnOnConsecutiveCalls('2016-10-23 17:10:00', '2016-10-23 17:10:00');

        // mock the subject configuration
        $mockConfiguration = $this->getMockBuilder(SubjectConfigurationInterface::class)->getMock();
        $mockConfiguration->expects($this->any())->method('hasParam')->with(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)->willReturn(false);

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'hasHeader',
                                    'getHeader',
                                    'getHeaders',
                                    'hasBeenProcessed',
                                    'getAttributeSet',
                                    'getRow',
                                    'getDateConverter',
                                    'getConfiguration'
                                )
                            )
                            ->disableOriginalConstructor()
                            ->getMock();

        $mockSubject->expects($this->any())
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
                    ->will($this->returnCallback(function ($param) use ($headers) { return isset($headers[$param]); }));
        $mockSubject->expects($this->any())
                    ->method('getHeader')
                    ->will($this->returnCallback(function ($param) use ($headers) { return $headers[$param]; }));
        $mockSubject->expects($this->once())
                    ->method('hasBeenProcessed')
                    ->willReturn(false);
        $mockSubject->expects($this->once())
                    ->method('getAttributeSet')
                    ->willReturn(
                        array(
                            MemberNames::ATTRIBUTE_SET_ID   => 15,
                            MemberNames::ATTRIBUTE_SET_NAME => $attributeSetCode
                        )
                    );
        $mockSubject->expects($this->exactly(2))
            ->method('getDateConverter')
            ->willReturn($mockDateConverter);

        // mock the processor methods
        $this->mockProductBunchProcessor->expects($this->once())
                                        ->method('loadProduct')
                                        ->with($sku)
                                        ->willReturn($oldProduct);
        $this->mockProductBunchProcessor->expects($this->once())
                                        ->method('persistProduct')
                                        ->with($newProduct);

        // query whether or not the result is as expected
        $this->assertEquals($row, $this->observer->handle($mockSubject));
    }
}
