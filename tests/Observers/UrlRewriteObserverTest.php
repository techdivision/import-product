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

use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Utils\EntityStatus;

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
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->observer = new UrlRewriteObserver();
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
                                    'getRowStoreId',
                                    'persistUrlRewrite',
                                    'getRootCategory',
                                    'getCategory'
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
                        array(ColumnKeys::URL_KEY),
                        array(ColumnKeys::STORE_VIEW_CODE)
                    )
                    ->willReturnOnConsecutiveCalls(0, 1, 2);
        $mockSubject->expects($this->exactly(2))
                    ->method('getLastEntityId')
                    ->willReturn($entityId = 61413);
        $mockSubject->expects($this->once())
                    ->method('hasBeenProcessed')
                    ->willReturn(false);
        $mockSubject->expects($this->once())
                    ->method('getProductCategoryIds')
                    ->willReturn(array($categoryId = 2 => $entityId));
        $mockSubject->expects($this->once())
                    ->method('getRowStoreId')
                    ->willReturn($storeId = 1);
        $mockSubject->expects($this->once())
                    ->method('getCategory')
                    ->with($categoryId)
                    ->willReturn($category = array(MemberNames::ENTITY_ID => $categoryId, MemberNames::URL_PATH));
        $mockSubject->expects($this->exactly(4))
                    ->method('getRootCategory')
                    ->willReturn($category);
        $mockSubject->expects($this->once())
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
                    );

        // inject the subject und invoke the handle() method
        $this->observer->setSubject($mockSubject);
        $this->assertSame($row, $this->observer->handle($row));
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
                                    'persistUrlRewrite',
                                    'getRootCategory',
                                    'getRowStoreId',
                                    'getCategory'
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
                        array(ColumnKeys::URL_KEY),
                        array(ColumnKeys::STORE_VIEW_CODE)
                    )
                    ->willReturnOnConsecutiveCalls(0, 1, 2);
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
        $mockSubject->expects($this->exactly(4))
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
                    );

        // inject the subject und invoke the handle() method
        $this->observer->setSubject($mockSubject);
        $this->assertSame($row, $this->observer->handle($row));
    }
}
