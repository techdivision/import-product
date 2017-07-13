<?php

/**
 * TechDivision\Import\Product\Observers\UrlRewriteObserverTest
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
use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\CoreConfigDataKeys;

/**
 * Test class for the product URL rewrite observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class UrlRewriteObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The observer we want to test.
     *
     * @var \TechDivision\Import\Product\Observers\UrlRewriteObserver
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
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // initialize a mock processor instance
        $this->mockProductBunchProcessor = $this->getMockBuilder('TechDivision\Import\Product\Services\ProductBunchProcessorInterface')
                                                ->setMethods(get_class_methods('TechDivision\Import\Product\Services\ProductBunchProcessorInterface'))
                                                ->getMock();

        // initialize the observer
        $this->observer = new UrlRewriteObserver($this->mockProductBunchProcessor);
    }

    /**
     * Test's the handle() method with a successfull URL rewrite persist.
     *
     * @return void
     */
    public function testHandleWithSuccessfullCreateWithoutCategories()
    {

        // create a dummy CSV file header
        $headers = array(
            'sku'             => 0,
            'url_key'         => 1,
            'store_view_code' => 2
        );

        // create a dummy CSV file row
        $row = array(
            0 => 'TEST-01',
            1 => 'bruno-compete-hoodie-test',
            2 => null
        );

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'hasHeader',
                                    'getHeader',
                                    'getHeaders',
                                    'hasBeenProcessed',
                                    'getLastEntityId',
                                    'getProductCategoryIds',
                                    'getRootCategory',
                                    'getCategory',
                                    'getCoreConfigData',
                                    'getEntityType',
                                    'getRowStoreId',
                                    'getRow'
                                )
                            )
                            ->disableOriginalConstructor()
                            ->getMock();
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
                        array(ColumnKeys::URL_KEY),
                        array(ColumnKeys::URL_KEY),
                        array(ColumnKeys::STORE_VIEW_CODE)
                    )
                    ->willReturnOnConsecutiveCalls(0, 1, 1, 2);
        $mockSubject->expects($this->exactly(2))
                    ->method('getLastEntityId')
                    ->willReturn($entityId = 61413);
        $mockSubject->expects($this->once())
                    ->method('hasBeenProcessed')
                    ->willReturn(false);
        $mockSubject->expects($this->once())
                    ->method('getProductCategoryIds')
                    ->willReturn(array($categoryId = 2 => $entityId));
        $mockSubject->expects($this->exactly(2))
                    ->method('getRowStoreId')
                    ->willReturn($storeId = 1);
        $mockSubject->expects($this->once())
                    ->method('getCategory')
                    ->with($categoryId)
                    ->willReturn($category = array(MemberNames::ENTITY_ID => $categoryId, MemberNames::URL_PATH => null));
        $mockSubject->expects($this->exactly(4))
                    ->method('getRootCategory')
                    ->willReturn($category);
        $mockSubject->expects($this->exactly(2))
                    ->method('getCoreConfigData')
                    ->withConsecutive(
                        array(CoreConfigDataKeys::CATALOG_SEO_PRODUCT_USE_CATEGORIES, false),
                        array(CoreConfigDataKeys::CATALOG_SEO_PRODUCT_URL_SUFFIX, '.html')
                    )
                    ->willReturnOnConsecutiveCalls(true, '.html');
        $mockSubject->expects($this->once())
                    ->method('getEntityType')
                    ->willReturn(
                        array(
                            MemberNames::ENTITY_TYPE_ID => 1,
                            MemberNames::ENTITY_TYPE_CODE => EntityTypeCodes::CATALOG_PRODUCT
                        )
                    );

        // mock the processor methods
        $this->mockProductBunchProcessor->expects($this->once())
                    ->method('persistUrlRewrite')
                    ->with(
                        array(
                            MemberNames::ENTITY_TYPE      => UrlRewriteObserver::ENTITY_TYPE,
                            MemberNames::ENTITY_ID        => $entityId,
                            MemberNames::REQUEST_PATH     => sprintf('%s.html', $row[$headers[ColumnKeys::URL_KEY]]),
                            MemberNames::TARGET_PATH      => sprintf('catalog/product/view/id/%s', $entityId),
                            MemberNames::REDIRECT_TYPE    => 0,
                            MemberNames::STORE_ID         => $storeId,
                            MemberNames::DESCRIPTION      => null,
                            MemberNames::IS_AUTOGENERATED => 1,
                            MemberNames::METADATA         => serialize(array()),
                            EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                        )
                    )
                    ->willReturn($urlRewriteId = 1000);
        $this->mockProductBunchProcessor->expects($this->once())
                    ->method('persistUrlRewriteProductCategory')
                    ->with(
                        array(
                            MemberNames::URL_REWRITE_ID   => $urlRewriteId,
                            MemberNames::PRODUCT_ID       => $entityId,
                            MemberNames::CATEGORY_ID      => $categoryId,
                            EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                        )
                    );

        // invoke the handle() method
        $this->assertSame($row, $this->observer->handle($mockSubject));
    }

    /**
     * Test's the handle() method with a successfull URL rewrite persist when using the same categories.
     *
     * @return void
     */
    public function testHandleWithSuccessfullUpdateAndSameCategories()
    {

        // initialize the entity ID to use
        $entityId = 61413;

        // create a dummy CSV file row
        $headers = array(
            'sku'             => 0,
            'url_key'         => 1,
            'categories'      => 2,
            'store_view_code' => 3
        );

        // create a dummy CSV file header
        $row = array(
            0 => 'TEST-01',
            1 => 'bruno-compete-hoodie',
            2 => 'Default Category/Men/Tops/Hoodies & Sweatshirts,Default Category/Collections/Eco Friendly,Default Category',
            3 => null
        );

        // create a mock subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Subjects\BunchSubject')
                            ->setMethods(
                                array(
                                    'hasHeader',
                                    'getHeader',
                                    'getHeaders',
                                    'hasBeenProcessed',
                                    'getLastEntityId',
                                    'getProductCategoryIds',
                                    'getRootCategory',
                                    'getRowStoreId',
                                    'getCategory',
                                    'getCoreConfigData',
                                    'getEntityType',
                                    'getRow'
                                )
                            )
                            ->disableOriginalConstructor()
                            ->getMock();
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
                        array(ColumnKeys::URL_KEY),
                        array(ColumnKeys::URL_KEY),
                        array(ColumnKeys::STORE_VIEW_CODE)
                    )
                    ->willReturnOnConsecutiveCalls(0, 1, 1, 2);
        $mockSubject->expects($this->any())
                    ->method('getLastEntityId')
                    ->willReturn($entityId);
        $mockSubject->expects($this->any())
                    ->method('getCategory')
                    ->withConsecutive(array(2), array(16), array(37), array(13))
                    ->willReturnOnConsecutiveCalls(
                         array(MemberNames::ENTITY_ID =>  2, MemberNames::URL_PATH => null),
                         array(MemberNames::ENTITY_ID => 16, MemberNames::URL_PATH => 'men/tops-men/hoodies-and-sweatshirts-men'),
                         array(MemberNames::ENTITY_ID => 37, MemberNames::URL_PATH => 'collections/eco-friendly'),
                         array(MemberNames::ENTITY_ID => 13, MemberNames::URL_PATH => 'men/tops-men')
                     );
        $mockSubject->expects($this->once())
                    ->method('hasBeenProcessed')
                    ->willReturn(false);
        $mockSubject->expects($this->any())
                    ->method('getRootCategory')
                    ->willReturn(array(MemberNames::ENTITY_ID =>  2, MemberNames::URL_PATH => null));
        $mockSubject->expects($this->once())
                    ->method('getProductCategoryIds')
                    ->willReturn(array(2 => $entityId, 16 => $entityId, 37 => $entityId, 13 => $entityId));
        $mockSubject->expects($this->any())
                    ->method('getRowStoreId')
                    ->willReturn($storeId = 1);
        $mockSubject->expects($this->exactly(5))
                    ->method('getCoreConfigData')
                    ->withConsecutive(
                        array(CoreConfigDataKeys::CATALOG_SEO_PRODUCT_USE_CATEGORIES, false),
                        array(CoreConfigDataKeys::CATALOG_SEO_PRODUCT_URL_SUFFIX, '.html'),
                        array(CoreConfigDataKeys::CATALOG_SEO_PRODUCT_URL_SUFFIX, '.html'),
                        array(CoreConfigDataKeys::CATALOG_SEO_PRODUCT_URL_SUFFIX, '.html'),
                        array(CoreConfigDataKeys::CATALOG_SEO_PRODUCT_URL_SUFFIX, '.html')
                    )
                    ->willReturnOnConsecutiveCalls(true, '.html', '.html', '.html', '.html');
        $mockSubject->expects($this->exactly(4))
                    ->method('getEntityType')
                    ->willReturn(array(MemberNames::ENTITY_TYPE_ID => 1, MemberNames::ENTITY_TYPE_CODE => EntityTypeCodes::CATALOG_PRODUCT));

        // mock the processor methods
        $this->mockProductBunchProcessor->expects($this->exactly(4))
                    ->method('persistUrlRewrite')
                    ->withConsecutive(
                        array(
                            array(
                                MemberNames::ENTITY_TYPE      => UrlRewriteObserver::ENTITY_TYPE,
                                MemberNames::ENTITY_ID        => $entityId,
                                MemberNames::REQUEST_PATH     => sprintf('%s.html', $row[$headers[ColumnKeys::URL_KEY]]),
                                MemberNames::TARGET_PATH      => sprintf('catalog/product/view/id/%s', $entityId),
                                MemberNames::REDIRECT_TYPE    => 0,
                                MemberNames::STORE_ID         => $storeId,
                                MemberNames::DESCRIPTION      => null,
                                MemberNames::IS_AUTOGENERATED => 1,
                                MemberNames::METADATA         => serialize(array()),
                                EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                            )
                        ),
                        array(
                            array(
                                MemberNames::ENTITY_TYPE      => UrlRewriteObserver::ENTITY_TYPE,
                                MemberNames::ENTITY_ID        => $entityId,
                                MemberNames::REQUEST_PATH     => sprintf('men/tops-men/hoodies-and-sweatshirts-men/%s.html', $row[$headers[ColumnKeys::URL_KEY]]),
                                MemberNames::TARGET_PATH      => sprintf('catalog/product/view/id/%s/category/16', $entityId),
                                MemberNames::REDIRECT_TYPE    => 0,
                                MemberNames::STORE_ID         => $storeId,
                                MemberNames::DESCRIPTION      => null,
                                MemberNames::IS_AUTOGENERATED => 1,
                                MemberNames::METADATA         => serialize(array('category_id' => 16)),
                                EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                            )
                        ),
                        array(
                            array(
                                MemberNames::ENTITY_TYPE      => UrlRewriteObserver::ENTITY_TYPE,
                                MemberNames::ENTITY_ID        => $entityId,
                                MemberNames::REQUEST_PATH     => sprintf('collections/eco-friendly/%s.html', $row[$headers[ColumnKeys::URL_KEY]]),
                                MemberNames::TARGET_PATH      => sprintf('catalog/product/view/id/%s/category/37', $entityId),
                                MemberNames::REDIRECT_TYPE    => 0,
                                MemberNames::STORE_ID         => $storeId,
                                MemberNames::DESCRIPTION      => null,
                                MemberNames::IS_AUTOGENERATED => 1,
                                MemberNames::METADATA         => serialize(array('category_id' => 37)),
                                EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                            )
                        ),
                        array(
                            array(
                                MemberNames::ENTITY_TYPE      => UrlRewriteObserver::ENTITY_TYPE,
                                MemberNames::ENTITY_ID        => $entityId,
                                MemberNames::REQUEST_PATH     => sprintf('men/tops-men/%s.html', $row[$headers[ColumnKeys::URL_KEY]]),
                                MemberNames::TARGET_PATH      => sprintf('catalog/product/view/id/%s/category/13', $entityId),
                                MemberNames::REDIRECT_TYPE    => 0,
                                MemberNames::STORE_ID         => $storeId,
                                MemberNames::DESCRIPTION      => null,
                                MemberNames::IS_AUTOGENERATED => 1,
                                MemberNames::METADATA         => serialize(array('category_id' => 13)),
                                EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                            )
                        )
                    )
                    ->willReturnOnConsecutiveCalls(1000, 1001, 1002, 1003);
        $this->mockProductBunchProcessor->expects($this->exactly(4))
                    ->method('persistUrlRewriteProductCategory')
                    ->withConsecutive(
                        array(
                            array(
                                MemberNames::URL_REWRITE_ID   => 1000,
                                MemberNames::PRODUCT_ID       => $entityId,
                                MemberNames::CATEGORY_ID      => 2,
                                EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                            )
                        ),
                        array(
                            array(
                                MemberNames::URL_REWRITE_ID   => 1001,
                                MemberNames::PRODUCT_ID       => $entityId,
                                MemberNames::CATEGORY_ID      => 16,
                                EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                            )
                        ),
                        array(
                            array(
                                MemberNames::URL_REWRITE_ID   => 1002,
                                MemberNames::PRODUCT_ID       => $entityId,
                                MemberNames::CATEGORY_ID      => 37,
                                EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                            )
                        ),
                        array(
                            array(
                                MemberNames::URL_REWRITE_ID   => 1003,
                                MemberNames::PRODUCT_ID       => $entityId,
                                MemberNames::CATEGORY_ID      => 13,
                                EntityStatus::MEMBER_NAME     => EntityStatus::STATUS_CREATE
                            )
                        )
                    );

        // invoke the handle() method
        $this->assertSame($row, $this->observer->handle($mockSubject));
    }
}
