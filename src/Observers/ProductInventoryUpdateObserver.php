<?php

/**
 * TechDivision\Import\Product\Observers\ProductInventoryUpdateObserver
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
 * Observer that creates/updates the product's inventory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductInventoryUpdateObserver extends ProductInventoryObserver
{

    /**
     * Initialize the stock item with the passed attributes and returns an instance.
     *
     * @param array $attr The stock item attributes
     *
     * @return array The initialized stock item
     */
    protected function initializeStockItem(array $attr)
    {

        // load the stock item with the passed item/product/stock ID
        $entity = $this->loadStockItem(
            $attr[MemberNames::PRODUCT_ID],
            $attr[MemberNames::WEBSITE_ID],
            $attr[MemberNames::STOCK_ID]
        );

        // merge the attributes with the entity, if available
        if ($entity) {
            return $this->mergeEntity($entity, $attr);
        }

        // otherwise simply return the attributes
        return $attr;
    }

    /**
     * Initialize the stock item status with the passed attributes and returns an instance.
     *
     * @param array $attr The stock item attributes
     *
     * @return array The initialized stock item
     */
    protected function initializeStockItemStatus(array $attr)
    {

        // load the stock item with the passed item/product/stock ID
        $entity = $this->loadStockItemStatus(
            $attr[MemberNames::PRODUCT_ID],
            $attr[MemberNames::WEBSITE_ID],
            $attr[MemberNames::STOCK_ID]
        );

        // merge the attributes with the entity, if available
        if ($entity) {
            return $this->mergeEntityStatus($entity, $attr);
        }

        // otherwise simply return the attributes
        return $attr;
    }

    /**
     * Load's and return's the stock item with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock item to load
     * @param integer $websiteId The website ID of the stock item to load
     * @param integer $stockId   The stock ID of the stock item to load
     *
     * @return array The stock item
     */
    protected function loadStockItem($productId, $websiteId, $stockId)
    {
        return $this->getProductBunchProcessor()->loadStockItem($productId, $websiteId, $stockId);
    }

    /**
     * Load's and return's the stock item status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock item to load
     * @param integer $websiteId The website ID of the stock item to load
     * @param integer $stockId   The stock ID of the stock item to load
     *
     * @return array The stock item
     */
    protected function loadStockItemStatus($productId, $websiteId, $stockId)
    {
        return $this->getProductBunchProcessor()->loadStockItemStatus($productId, $websiteId, $stockId);
    }
}
