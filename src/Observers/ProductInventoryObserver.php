<?php

/**
 * TechDivision\Import\Product\Observers\ProductInventoryObserver
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
use TechDivision\Import\Observers\AttributeLoaderInterface;
use TechDivision\Import\Observers\DynamicAttributeObserverInterface;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;

/**
 * Observer that creates/updates the product's inventory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductInventoryObserver extends AbstractProductImportObserver implements DynamicAttributeObserverInterface
{

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /**
     * The attribute loader instance.
     *
     * @var \TechDivision\Import\Observers\AttributeLoaderInterface
     */
    protected $attributeLoader;

    /**
     * Initialize the observer with the passed product bunch processor instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface $productBunchProcessor The product bunch processor instance
     * @param \TechDivision\Import\Observers\AttributeLoaderInterface              $attributeLoader       The attribute loader instance
     */
    public function __construct(
        ProductBunchProcessorInterface $productBunchProcessor,
        AttributeLoaderInterface $attributeLoader
    ) {
        $this->productBunchProcessor = $productBunchProcessor;
        $this->attributeLoader = $attributeLoader;
    }

    /**
     * Return's the product bunch processor instance.
     *
     * @return \TechDivision\Import\Product\Services\ProductBunchProcessorInterface The product bunch processor instance
     */
    protected function getProductBunchProcessor()
    {
        return $this->productBunchProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->hasBeenProcessed($this->getValue(ColumnKeys::SKU))) {
            return;
        }

        // prepare, initialize and persist the stock status/item
        $this->persistStockItem($this->initializeStockItem($this->prepareStockItemAttributes()));
    }

    /**
     * Prepare the basic attributes of the stock status/item entity that has to be persisted.
     *
     * @return array The prepared stock status/item attributes
     */
    protected function prepareAttributes()
    {

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getSubject()->getLastEntityId();

        // initialize the stock status data
        $websiteId =  $this->getValue(ColumnKeys::WEBSITE_ID, 0);

        // return the prepared stock status
        return $this->initializeEntity(
            array(
                MemberNames::PRODUCT_ID   => $lastEntityId,
                MemberNames::WEBSITE_ID   => $websiteId,
                MemberNames::STOCK_ID     => 1
            )
        );
    }

    /**
     * Prepare the stock item attributes of the entity that has to be persisted.
     *
     * @return array The prepared stock status item
     */
    protected function prepareStockItemAttributes()
    {
        return array_merge($this->prepareAttributes(), $this->attributeLoader->load($this, $this->getHeaderStockMappings()));
    }

    /**
     * Initialize the stock item with the passed attributes and returns an instance.
     *
     * @param array $attr The stock item attributes
     *
     * @return array The initialized stock item
     */
    protected function initializeStockItem(array $attr)
    {
        return $attr;
    }

    /**
     * Return's the appings for the table column => CSV column header.
     *
     * @return array The header stock mappings
     */
    protected function getHeaderStockMappings()
    {
        return $this->getSubject()->getHeaderStockMappings();
    }

    /**
     * Persist's the passed stock item data and return's the ID.
     *
     * @param array $stockItem The stock item data to persist
     *
     * @return void
     */
    protected function persistStockItem($stockItem)
    {
        $this->getProductBunchProcessor()->persistStockItem($stockItem);
    }
}
