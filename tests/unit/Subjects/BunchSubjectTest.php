<?php

/**
 * TechDivision\Import\Product\Subjects\BunchSubjectTest
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Subjects;

use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\RegistryKeys;
use TechDivision\Import\Subjects\AbstractTest;
use TechDivision\Import\Utils\Generators\CoreConfigDataUidGenerator;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Utils\Mappings\MapperInterface;

/**
 * Test class for the product action implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class BunchSubjectTest extends AbstractTest
{

    /**
     * The subject we want to test.
     *
     * @var \TechDivision\Import\Product\Subjects\BunchSubject
     */
    protected $subject;

    /**
     * Mock the global data.
     *
     * @return array The array with the global data
     */
    protected function getMockGlobalData(array $globalData = array())
    {
        return parent::getMockGlobalData(
            array(
                RegistryKeys::GLOBAL_DATA => array(
                    RegistryKeys::LINK_TYPES => array(),
                    RegistryKeys::LINK_ATTRIBUTES => array(),
                    RegistryKeys::CATEGORIES => array(),
                    RegistryKeys::IMAGE_TYPES => array(),
                    RegistryKeys::TAX_CLASSES => array(),
                    RegistryKeys::STORE_WEBSITES => array(),
                    RegistryKeys::EAV_ATTRIBUTES => array(),
                    RegistryKeys::ATTRIBUTE_SETS => array(),
                    RegistryKeys::STORES => array(),
                    RegistryKeys::DEFAULT_STORE => array(),
                    RegistryKeys::ROOT_CATEGORIES => array(),
                    RegistryKeys::CORE_CONFIG_DATA => array(),
                    RegistryKeys::EAV_USER_DEFINED_ATTRIBUTES => array()
                )
            )
        );
    }

    /**
     * The class name of the subject we want to test.
     *
     * @return string The class name of the subject
     */
    protected function getSubjectClassName()
    {
        return 'TechDivision\Import\Product\Subjects\BunchSubject';
    }

    /**
     * Return the subject's methods we want to mock.
     *
     * @return array The methods
     */
    protected function getSubjectMethodsToMock()
    {
        return array(
            'touch',
            'write',
            'rename',
            'isFile',
            'getHeaderMappings',
            'getExecutionContext',
            'getDefaultCallbackMappings'
        );
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {

        // create the subject instance we want to test and invoke the setup method
        $this->subject = $this->getSubjectInstance();
        $this->subject->setUp($this->serial = uniqid());
    }


    /**
     * Mock the subject constructor args.
     *
     * @return array The subject constructor args
     */
    protected function getMockSubjectConstructorArgs()
    {

        // mock the registry processor
        $mockRegistryProcessor = $this->getMockRegistryProcessor();

        // mock the generator
        $mockGenerator = new CoreConfigDataUidGenerator();

        // mock the loggers
        $mockLoggers = $this->getMockLoggers();

        // mock the event emitter
        $mockEmitter = $this->getMockBuilder('League\Event\EmitterInterface')
            ->setMethods(\get_class_methods('League\Event\EmitterInterface'))
            ->getMock();

        // create a mock loader instance
        $mockLoader = $this->getMockBuilder(LoaderInterface::class)->getMock();

        // create a mock mapper instance
        $mockMapper = $this->getMockBuilder(MapperInterface::class)->getMock();
        $mockMapper->method('map')->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

        // prepare the constructor arguments
        return array(
            $mockRegistryProcessor,
            $mockGenerator,
            $mockLoggers,
            $mockEmitter,
            $mockLoader,
            $mockMapper
        );
    }

    /**
     * Test's the getEntityType() method successfull.
     *
     * @return void
     */
    public function testGetEntityType()
    {

        // initialize the expected entity type
        $entityType = array(
            MemberNames::ENTITY_TYPE_ID => 4,
            MemberNames::ENTITY_TYPE_CODE => EntityTypeCodes::CATALOG_PRODUCT
        );

        // query whether or not the entity type is available
        $this->assertEquals($entityType, $this->subject->getEntityType());
    }

    /**
     * Test the mapAttributeCodeByHeaderMapping() method.
     *
     * @return void
     * @dataProvider headerMappingProvider()
     */
    public function testMapAttributeCodeByHeaderMapping($columnName, $attributeCode)
    {

        // mock the getHeaderMappings() method
        $this->subject->expects($this->once())
            ->method('getHeaderMappings')
            ->willReturn(
                array(
                    'product_online'       => 'status',
                    'tax_class_name'       => 'tax_class_id',
                    'bundle_price_type'    => 'price_type',
                    'bundle_sku_type'      => 'sku_type',
                    'bundle_price_view'    => 'price_view',
                    'bundle_weight_type'   => 'weight_type',
                    'bundle_shipment_type' => 'shipment_type',
                    'related_skus'         => 'relation_skus',
                    'related_position'     => 'relation_position',
                    'crosssell_skus'       => 'cross_sell_skus',
                    'crosssell_position'   => 'cross_sell_position',
                    'upsell_skus'          => 'up_sell_skus',
                    'upsell_position'      => 'up_sell_position',
                    'msrp_price'           => 'msrp',
                    'base_image'           => 'image',
                    'base_image_label'     => 'image_label',
                    'thumbnail_image'      => 'thumbnail',
                    'thumbnail_image_label'=> 'thumbnail_label'
                )
            );

        // try to map the values
        $this->assertSame($attributeCode, $this->subject->mapAttributeCodeByHeaderMapping($columnName));
    }

    /**
     * Data provider for column name => attribute code mappings.
     *
     * @return array The mappings
     */
    public function headerMappingProvider()
    {
        return array(
            array('product_online', 'status'),
            array('tax_class_name', 'tax_class_id'),
            array('bundle_price_type', 'price_type'),
            array('bundle_sku_type', 'sku_type'),
            array('bundle_price_view', 'price_view'),
            array('bundle_weight_type', 'weight_type'),
            array('base_image', 'image'),
            array('base_image_label', 'image_label'),
            array('thumbnail_image', 'thumbnail'),
            array('thumbnail_image_label', 'thumbnail_label'),
            array('bundle_shipment_type', 'shipment_type'),
            array('related_skus', 'relation_skus'),
            array('related_position', 'relation_position'),
            array('crosssell_skus', 'cross_sell_skus'),
            array('crosssell_position', 'cross_sell_position'),
            array('upsell_skus', 'up_sell_skus'),
            array('upsell_position', 'up_sell_position')
        );
    }
}
