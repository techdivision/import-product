<?php

/**
 * TechDivision\Import\Product\Observers\PreImport\QuantityAndStockStatusObserver
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

namespace TechDivision\Import\Product\Observers\PreImport;

use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;

/**
 * Observer that prepares the inventory information found in the CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class QuantityAndStockStatusObserver extends AbstractProductImportObserver
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

        /*
        $qty = (float) $row[$this->headers[ColumnKeys::QTY]];
        $isInStock = (integer) $row[$this->headers[ColumnKeys::IS_IN_STOCK]];

        $this->getSystemLogger()->info("Found qty $qty and is_in_stock $isInStock");

        $quantityAndStockStatus = 0;
        if ($qty > 0 && $isInStock === 1) {
            $quantityAndStockStatus = 1;
        }
        */

        // try to load the appropriate key for the stock status
        if (isset($headers[ColumnKeys::QUANTITY_AND_STOCK_STATUS])) {
            $newKey = $headers[ColumnKeys::QUANTITY_AND_STOCK_STATUS];
        } else {
            $headers[ColumnKeys::QUANTITY_AND_STOCK_STATUS] = $newKey = sizeof($headers);
        }

        // append/replace the stock status
        $row[$newKey] = 1;

        // update the header information
        $this->setHeaders($headers);

        // return the prepared row
        return $row;
    }
}
