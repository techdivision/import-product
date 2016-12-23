<?php

/**
 * TechDivision\Import\Product\Observers\ProductUpdateObserver
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

/**
 * Observer that add's/update's the product itself.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductUpdateObserver extends ProductObserver
{

    /**
     * Initialize the product with the passed data and returns an instance.
     *
     * @param string  $sku             The product's SKU
     * @param string  $createdAt       The product's creation date
     * @param string  $updatedAt       The product's last update date
     * @param integer $hasOptions      Marks the product to has options
     * @param integer $requiredOptions Marks the product that some of the options are required
     * @param string  $typeId          The product's type ID
     * @param integer $attributeSetId  The product's attribute set ID
     *
     * @return array The initialized product
     */
    public function initializeProduct(
        $sku,
        $createdAt,
        $updatedAt,
        $hasOptions,
        $requiredOptions,
        $typeId,
        $attributeSetId
    ) {

        // initialize and the product data
        $data = parent::initializeProduct(
            $sku,
            $createdAt,
            $updatedAt,
            $hasOptions,
            $requiredOptions,
            $typeId,
            $attributeSetId
        );

        // load the product with the passed SKU and merge it with the data
        if ($product = $this->loadProduct($sku)) {
            return array_merge($product, $data);
        }

        // otherwise simply return the data
        return $data;
    }

    /**
     * Load's and return's the product with the passed SKU.
     *
     * @param string $sku The SKU of the product to load
     *
     * @return array The product
     */
    public function loadProduct($sku)
    {
        return $this->getSubject()->loadProduct($sku);
    }
}
