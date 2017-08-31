<?php

/**
 * TechDivision\Import\Product\Observers\ProductAttributeUpdateObserver
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
 * Observer that creates/updates the product's attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductAttributeUpdateObserver extends ProductAttributeObserver
{

    /**
     * Initialize the category product with the passed attributes and returns an instance.
     *
     * @param array $attr The category product attributes
     *
     * @return array The initialized category product
     */
    protected function initializeAttribute(array $attr)
    {

        // load the supported backend types
        $backendTypes = $this->getBackendTypes();

        // initialize the persist method for the found backend type
        list (, $loadMethod) = $backendTypes[$this->backendType];

        // load store/entity/attribute ID
        $storeId = $attr[MemberNames::STORE_ID];
        $entityId = $attr[MemberNames::ENTITY_ID];
        $attributeId = $attr[MemberNames::ATTRIBUTE_ID];

        // try to load the attribute with the passed entity/attribute/store ID
        // and merge it with the attributes
        if ($entity = $this->$loadMethod($entityId, $attributeId, $storeId)) {
            return $this->mergeEntity($entity, $attr);
        }

        // otherwise simply return the attributes
        return $attr;
    }

    /**
     * Load's and return's the datetime attribute with the passed entity/attribute/store ID.
     *
     * @param integer $pk          The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The datetime attribute
     */
    protected function loadDatetimeAttribute($pk, $attributeId, $storeId)
    {
        return $this->getProductBunchProcessor()->loadProductDatetimeAttribute($pk, $attributeId, $storeId);
    }

    /**
     * Load's and return's the decimal attribute with the passed entity/attribute/store ID.
     *
     * @param integer $pk          The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The decimal attribute
     */
    protected function loadDecimalAttribute($pk, $attributeId, $storeId)
    {
        return $this->getProductBunchProcessor()->loadProductDecimalAttribute($pk, $attributeId, $storeId);
    }

    /**
     * Load's and return's the integer attribute with the passed entity/attribute/store ID.
     *
     * @param integer $pk          The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The integer attribute
     */
    protected function loadIntAttribute($pk, $attributeId, $storeId)
    {
        return $this->getProductBunchProcessor()->loadProductIntAttribute($pk, $attributeId, $storeId);
    }

    /**
     * Load's and return's the text attribute with the passed entity/attribute/store ID.
     *
     * @param integer $pk          The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The text attribute
     */
    protected function loadTextAttribute($pk, $attributeId, $storeId)
    {
        return $this->getProductBunchProcessor()->loadProductTextAttribute($pk, $attributeId, $storeId);
    }

    /**
     * Load's and return's the varchar attribute with the passed entity/attribute/store ID.
     *
     * @param integer $pk          The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The varchar attribute
     */
    protected function loadVarcharAttribute($pk, $attributeId, $storeId)
    {
        return $this->getProductBunchProcessor()->loadProductVarcharAttribute($pk, $attributeId, $storeId);
    }
}
