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
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;

/**
 * Observer that creates/updates the product's inventory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductInventoryObserver extends AbstractProductImportObserver
{

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->isLastSku($this->getValue(ColumnKeys::SKU))) {
            return;
        }

        // prepare, initialize and persist the stock status/item
        $this->persistStockStatus($this->initializeStockStatus($this->prepareStockStatusAttributes()));
        $this->persistStockItem($this->initializeStockItem($this->prepareStockItemAttributes()));
    }

    /**
     * Prepare the stock status attributes of the entity that has to be persisted.
     *
     * @return array The prepared stock status attributes
     */
    protected function prepareStockStatusAttributes()
    {

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getLastEntityId();

        // initialize the stock status data
        $websiteId =  $this->getValue(ColumnKeys::WEBSITE_ID);
        $qty = $this->castValueByBackendType('float', $this->getValue(ColumnKeys::QTY));

        // return the prepared stock status
        return $this->initializeEntity(
            array(
                MemberNames::PRODUCT_ID   => $lastEntityId,
                MemberNames::WEBSITE_ID   => $websiteId,
                MemberNames::STOCK_ID     => 1,
                MemberNames::STOCK_STATUS => $qty > 0 ? 1 : 0,
                MemberNames::QTY          => $qty
            )
        );
    }

    /**
     * Initialize the stock status with the passed attributes and returns an instance.
     *
     * @param array $attr The stock status attributes
     *
     * @return array The initialized stock status
     */
    protected function initializeStockStatus(array $attr)
    {
        return $attr;
    }

    /**
     * Prepare the stock item attributes of the entity that has to be persisted.
     *
     * @return array The prepared stock status item
     */
    protected function prepareStockItemAttributes()
    {

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getLastEntityId();

        // initialize the stock status data
        $websiteId =  $this->getValue(ColumnKeys::WEBSITE_ID);

        // initialize the stock item with the basic data
        $stockItem = $this->initializeEntity(
            array(
                MemberNames::PRODUCT_ID  => $lastEntityId,
                MemberNames::WEBSITE_ID  => $websiteId,
                MemberNames::STOCK_ID    => 1
            )
        );

        // append the row values to the stock item
        $headerStockMappings = $this->getHeaderStockMappings();
        foreach ($headerStockMappings as $columnName => $header) {
            list ($headerName, $backendType) = $header;
            $stockItem[$columnName] = $this->castValueByBackendType($backendType, $this->getValue($headerName));
        }

        // return the prepared stock item
        return $stockItem;
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
     * Persist's the passed stock item data and return's the ID.
     *
     * @param array $stockItem The stock item data to persist
     *
     * @return void
     */
    protected function persistStockItem($stockItem)
    {
        $this->getSubject()->persistStockItem($stockItem);
    }

    /**
     * Persist's the passed stock status data and return's the ID.
     *
     * @param array $stockStatus The stock status data to persist
     *
     * @return void
     */
    protected function persistStockStatus($stockStatus)
    {
        $this->getSubject()->persistStockStatus($stockStatus);
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
}
