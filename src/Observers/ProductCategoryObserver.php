<?php

/**
 * TechDivision\Import\Product\Observers\ProductCategoryObserver
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
 * Observer that creates/updates the product's category relations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductCategoryObserver extends AbstractProductImportObserver
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

        // query whether or not, product => category relations has been specified
        if (isset($row[$headers[ColumnKeys::CATEGORIES]])) {
            $productCategories = $row[$headers[ColumnKeys::CATEGORIES]];
        }

        // if not, we dont do anything
        if (empty($productCategories)) {
            return $row;
        }

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getLastEntityId();

        // extract the category trees and try to import the data
        $paths = explode(',', $productCategories);
        foreach ($paths as $path) {
            // load the category for the found path
            $category = $this->getCategoryByPath(trim($path));
            // relate the found category with the product
            $this->persistProductCategory(array($categoryId = $category[MemberNames::ENTITY_ID], $lastEntityId, 0));
            // add the category ID to the list
            $this->addProductCategoryId($categoryId);
        }

        // returns the row
        return $row;
    }

    /**
     * Add the passed category ID to the product's category list.
     *
     * @param integer $categoryId The category ID to add
     *
     * @return void
     */
    public function addProductCategoryId($categoryId)
    {
        $this->getSubject()->addProductCategoryId($categoryId);
    }

    /**
     * Persist's the passed product category data and return's the ID.
     *
     * @param array $productCategory The product category data to persist
     *
     * @return void
     */
    public function persistProductCategory($productCategory)
    {
        $this->getSubject()->persistProductCategory($productCategory);
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     */
    public function getCategoryByPath($path)
    {
        return $this->getSubject()->getCategoryByPath($path);
    }
}
