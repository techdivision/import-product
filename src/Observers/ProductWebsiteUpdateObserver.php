<?php

/**
 * TechDivision\Import\Product\Observers\ProductWebsiteUpdateObserver
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

use TechDivision\Import\Product\Utils\MemberNames;

/**
 * Observer that creates/updates the product's website relations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductWebsiteUpdateObserver extends ProductWebsiteObserver
{

    /**
     * Initialize the product website with the passed attributes and returns an instance.
     *
     * @param array $attr The product website attributes
     *
     * @return array The initialized product website
     * @throws \RuntimeException Is thrown, if the attributes can not be initialized
     */
    protected function initializeProductWebsite(array $attr)
    {

        // try to load the product website relation with the passed product/website ID
        if ($this->loadProductWebsite($attr[MemberNames::PRODUCT_ID], $attr[MemberNames::WEBSITE_ID])) {
            // throw a runtime exception, if the relation already exists
            throw new \RuntimeException(
                sprintf(
                    'Product website relation %d => %d already exsits',
                    $attr[MemberNames::PRODUCT_ID],
                    $attr[MemberNames::WEBSITE_ID]
                )
            );
        }

        // otherwise simply return the attributes
        return $attr;
    }

    /**
     * Load's and return's the product website relation with the passed product and website ID.
     *
     * @param string $productId The product ID of the relation
     * @param string $websiteId The website ID of the relation
     *
     * @return array The product website
     */
    protected function loadProductWebsite($productId, $websiteId)
    {
        return $this->getProductBunchProcessor()->loadProductWebsite($productId, $websiteId);
    }
}
