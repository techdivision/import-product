<?php

/**
 * TechDivision\Import\Product\Observers\ProductWebsiteObserver
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
 * Observer that creates/updates the product's website relations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductWebsiteObserver extends AbstractProductImportObserver
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

        // query whether or not, product => website relations has been specified
        if (isset($row[$headers[ColumnKeys::PRODUCT_WEBSITES]])) {
            $productWebsites = $row[$headers[ColumnKeys::PRODUCT_WEBSITES]];
        }

        // if not, we dont do anything
        if (empty($productWebsites)) {
            return $row;
        }

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getLastEntityId();

        // append the product => website relations found
        $codes = explode(',', $productWebsites);
        foreach ($codes as $code) {
            // load the website ID to relate the product with
            $websiteId = $this->getStoreWebsiteIdByCode($code);

            // create the product website relation
            $this->persistProductWebsite(array($lastEntityId, $websiteId));
        }

        // returns the row
        return $row;
    }

    /**
     * Persist's the passed product website data and return's the ID.
     *
     * @param array $productWebsite The product website data to persist
     *
     * @return void
     */
    public function persistProductWebsite($productWebsite)
    {
        $this->getSubject()->persistProductWebsite($productWebsite);
    }

    /**
     * Return's the store website for the passed code.
     *
     * @param string $code The code of the store website to return the ID for
     *
     * @return integer The store website ID
     */
    public function getStoreWebsiteIdByCode($code)
    {
        return $this->getSubject()->getStoreWebsiteIdByCode($code);
    }
}
