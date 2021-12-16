<?php

/**
 * TechDivision\Import\Product\Observers\ProductAttributeUpdateObserver
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
 * Observer that creates/updates the product's attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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

        // try to load the attribute with the passed attribute ID and merge it with the attributes
        if (isset($this->attributes[$attributeId = (integer) $attr[MemberNames::ATTRIBUTE_ID]])) {
            return $this->mergeEntity($this->attributes[$attributeId], $attr);
        }

        // otherwise simply return the attributes
        return $attr;
    }

    /**
     * Intializes the existing attributes for the entity with the passed primary key.
     *
     * @param string  $pk      The primary key of the entity to load the attributes for
     * @param integer $storeId The ID of the store view to load the attributes for
     *
     * @return array The entity attributes
     */
    protected function getAttributesByPrimaryKeyAndStoreId($pk, $storeId)
    {
        $this->attributes = $this->getProductBunchProcessor()->getProductAttributesByPrimaryKeyAndStoreId($pk, $storeId);
    }
}
