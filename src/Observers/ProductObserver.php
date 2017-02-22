<?php

/**
 * TechDivision\Import\Product\Observers\ProductObserver
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
 * Observer that create's the product itself.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductObserver extends AbstractProductImportObserver
{

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->hasBeenProcessed($this->getValue(ColumnKeys::SKU))) {
            return;
        }

        // prepare the static entity values
        $product = $this->initializeProduct($this->prepareAttributes());

        // insert the entity and set the entity ID
        $this->setLastEntityId($this->persistProduct($product));
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    protected function prepareAttributes()
    {

        // prepare the date format for the created at/updated at dates
        $createdAt = $this->getValue(ColumnKeys::CREATED_AT, date('Y-m-d H:i:s'), array($this, 'formatDate'));
        $updatedAt = $this->getValue(ColumnKeys::UPDATED_AT, date('Y-m-d H:i:s'), array($this, 'formatDate'));

        // initialize the product values
        $sku = $this->getValue(ColumnKeys::SKU);
        $productType = $this->getValue(ColumnKeys::PRODUCT_TYPE);

        // load the product's attribute set ID
        $attributeSet = $this->getAttributeSet();
        $attributeSetId = $attributeSet[MemberNames::ATTRIBUTE_SET_ID];

        // return the prepared product
        return $this->initializeEntity(
            array(
                MemberNames::SKU              => $sku,
                MemberNames::CREATED_AT       => $createdAt,
                MemberNames::UPDATED_AT       => $updatedAt,
                MemberNames::HAS_OPTIONS      => 0,
                MemberNames::REQUIRED_OPTIONS => 0,
                MemberNames::TYPE_ID          => $productType,
                MemberNames::ATTRIBUTE_SET_ID => $attributeSetId
            )
        );
    }

    /**
     * Initialize the product with the passed attributes and returns an instance.
     *
     * @param array $attr The product attributes
     *
     * @return array The initialized product
     */
    protected function initializeProduct(array $attr)
    {
        return $attr;
    }

    /**
     * Persist's the passed product data and return's the ID.
     *
     * @param array $product The product data to persist
     *
     * @return string The ID of the persisted entity
     */
    protected function persistProduct($product)
    {
        return $this->getSubject()->persistProduct($product);
    }

    /**
     * Return's the attribute set of the product that has to be created.
     *
     * @return array The attribute set
     */
    protected function getAttributeSet()
    {
        return $this->getSubject()->getAttributeSet();
    }

    /**
     * Set's the ID of the product that has been created recently.
     *
     * @param string $lastEntityId The entity ID
     *
     * @return void
     */
    protected function setLastEntityId($lastEntityId)
    {
        $this->getSubject()->setLastEntityId($lastEntityId);
    }
}
