<?php

/**
 * TechDivision\Import\Product\Observers\PreImport\ClearUrlRewriteObserver
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
use TechDivision\Import\Product\Utils\SqlStatements;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ClearUrlRewriteObserver extends AbstractProductImportObserver
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
        if ($this->isLastSku($sku = $row[$headers[ColumnKeys::SKU]])) {
            return $row;
        }

        // remove the product with the passed SKU
        $this->removeUrlRewrite(array($sku), SqlStatements::REMOVE_URL_REWRITE_BY_SKU);

        // return the prepared row
        return $row;
    }

    /**
     * Remove's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to remove
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function removeUrlRewrite($row, $name = null)
    {
        $this->getSubject()->removeUrlRewrite($row, $name);
    }
}
