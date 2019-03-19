<?php

/**
 * TechDivision\Import\Product\Subjects\BunchSubjectTest
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

namespace TechDivision\Import\Product\Subjects;

use Doctrine\Common\Collections\ArrayCollection;
use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\Product\Utils\RegistryKeys;
use TechDivision\Import\Product\Utils\MemberNames;

/**
 * Test class for the product action implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class BunchSubjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The subject we want to test.
     *
     * @var \TechDivision\Import\Product\Subjects\BunchSubject
     */
    protected $subject;

    /**
     * The global data.
     *
     * @var array
     */
    protected $globalData = array(
        RegistryKeys::GLOBAL_DATA => array(
            RegistryKeys::ENTITY_TYPES => array(
                EntityTypeCodes::CATALOG_PRODUCT => array(
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::ENTITY_TYPE_CODE => EntityTypeCodes::CATALOG_PRODUCT
                )
            ),
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
    );

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // create a mock registry processor
        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
                                      ->getMock();
        $mockRegistryProcessor->expects($this->any())
                              ->method('getAttribute')
                              ->with($serial = uniqid())
                              ->willReturn($this->globalData);

        // create a generator
        $mockGenerator = $this->getMockBuilder('TechDivision\Import\Utils\Generators\GeneratorInterface')
                              ->setMethods(get_class_methods('TechDivision\Import\Utils\Generators\GeneratorInterface'))
                              ->getMock();

        // create a mock configuration
        $mockConfiguration = $this->getMockBuilder($configurationInterface = 'TechDivision\Import\ConfigurationInterface')
                                  ->setMethods(get_class_methods($configurationInterface))
                                  ->getMock();
        $mockConfiguration->expects($this->any())
                          ->method('getEntityTypeCode')
                          ->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

        // create a mock subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder($subjectConfigurationInterface = 'TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods($subjectConfigurationInterface))
                                         ->getMock();
        $mockSubjectConfiguration->expects($this->any())
                                 ->method('getConfiguration')
                                 ->willReturn($mockConfiguration);
        $mockSubjectConfiguration->expects($this->any())
                                 ->method('getCallbacks')
                                 ->willReturn(array());
        $mockSubjectConfiguration->expects($this->any())
                                 ->method('getHeaderMappings')
                                 ->willReturn(array());
        $mockSubjectConfiguration->expects($this->any())
                                 ->method('getImageTypes')
                                 ->willReturn(array());
        $mockSubjectConfiguration->expects($this->any())
                                 ->method('getFrontendInputCallbacks')
                                 ->willReturn(array());

        // mock the event emitter
        $mockEmitter = $this->getMockBuilder('League\Event\EmitterInterface')
                            ->setMethods(\get_class_methods('League\Event\EmitterInterface'))
                            ->getMock();

        // create the subject to be tested
        $this->subject = new BunchSubject(
            $mockRegistryProcessor,
            $mockGenerator,
            new ArrayCollection(),
            $mockEmitter
        );

        // inject the mock configuration
        $this->subject->setConfiguration($mockSubjectConfiguration);

        // set-up the the subject
        $this->subject->setUp($serial);
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
