<?php

/**
 * TechDivision\Import\Product\ObserversInventoryObserver
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
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;

/**
 * A SLSB that handles the process to import product bunches.
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
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param array $row The row to handle
     *
     * @return array The modified row
     * @see \TechDivision\Import\Product\Observers\ImportObserverInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->isLastSku($row[$headers[ColumnKeys::SKU]])) {
            return $row;
        }

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getLastEntityId();

        // initialize the stock status data
        $websiteId =  $row[$headers[ColumnKeys::WEBSITE_ID]];
        $qty = $this->castValueByBackendType('float', $row[$headers[ColumnKeys::QTY]]);

        // initialize and persist the stock statuscreate the stock status
        $this->persistStockStatus(array($lastEntityId, $websiteId, 1, $qty, $qty > 0 ? 1 : 0));

        // initialize the stock item with the basic data
        $stockItem = array($lastEntityId, 1, $websiteId);

        // append the row values to the stock item
        $headerStockMappings = $this->getHeaderStockMappings();
        foreach ($headerStockMappings as $header) {
            list ($headerName, $backendType) = $header;
            $stockItem[] = $this->castValueByBackendType($backendType, $row[$headers[$headerName]]);
        }

        // create the stock item
        $this->persistStockItem($stockItem);

        // returns the row
        return $row;
    }

    /**
     * Persist's the passed stock item data and return's the ID.
     *
     * @param array $stockItem The stock item data to persist
     *
     * @return void
     */
    public function persistStockItem($stockItem)
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
    public function persistStockStatus($stockStatus)
    {
        $this->getSubject()->persistStockStatus($stockStatus);
    }

    /**
     * Return's the appings for the table column => CSV column header.
     *
     * @return array The header stock mappings
     */
    public function getHeaderStockMappings()
    {
        return $this->getSubject()->getHeaderStockMappings();
    }
}
