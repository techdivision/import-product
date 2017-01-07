<?php

/**
 * TechDivision\Import\Product\Observers\ProductInventoryUpdateObserver
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

use TechDivision\Import\Product\Utils\MemberNames;

/**
 * Observer that creates/updates the product's inventory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductInventoryUpdateObserver extends ProductInventoryObserver
{

    /**
     * Initialize the stock status with the passed attributes and returns an instance.
     *
     * @param array $attr The stock status attributes
     *
     * @return array The initialized stock status
     */
    protected function initializeStockStatus(array $attr)
    {

        // load the stock status with the passed product/website/stock ID
        $entity = $this->loadStockStatus(
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
     * Load's and return's the stock status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock status to load
     * @param integer $websiteId The website ID of the stock status to load
     * @param integer $stockId   The stock ID of the stock status to load
     *
     * @return array The stock status
     */
    protected function loadStockStatus($productId, $websiteId, $stockId)
    {
        return $this->getSubject()->loadStockStatus($productId, $websiteId, $stockId);
    }

    /**
     * Load's and return's the stock status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock item to load
     * @param integer $websiteId The website ID of the stock item to load
     * @param integer $stockId   The stock ID of the stock item to load
     *
     * @return array The stock item
     */
    protected function loadStockItem($productId, $websiteId, $stockId)
    {
        return $this->getSubject()->loadStockItem($productId, $websiteId, $stockId);
    }
}
