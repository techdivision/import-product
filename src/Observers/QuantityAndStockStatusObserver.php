<?php

/**
 * TechDivision\Import\Product\Observers\QuantityAndStockStatusObserver
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

use TechDivision\Import\Product\Utils\ColumnKeys;

/**
 * Observer that prepares the inventory information found in the CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class QuantityAndStockStatusObserver extends AbstractProductImportObserver
{

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // try to load the appropriate key for the stock status
        if (!$this->hasHeader(ColumnKeys::QUANTITY_AND_STOCK_STATUS)) {
            $this->addHeader(ColumnKeys::QUANTITY_AND_STOCK_STATUS);
        }

        // append/replace the stock status
        $this->setValue(ColumnKeys::QUANTITY_AND_STOCK_STATUS, 1);
    }
}
