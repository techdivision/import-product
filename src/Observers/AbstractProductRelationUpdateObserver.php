<?php

/**
 * TechDivision\Import\Product\Observers\VariantUpdateObserver
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
 * Oberserver that provides functionality for the product relation add/update operation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
abstract class AbstractProductRelationUpdateObserver extends AbstractProductRelationObserver
{

    /**
     * Initialize the product relation with the passed attributes and returns an instance.
     *
     * @param array $attr The product relation attributes
     *
     * @return array|null The initialized product relation, or null if the relation already exsist
     */
    protected function initializeProductRelation(array $attr)
    {

        // laod child/parent ID
        $childId = $attr[MemberNames::CHILD_ID];
        $parentId = $attr[MemberNames::PARENT_ID];

        // query whether or not the product relation already exists
        if ($this->loadProductRelation($parentId, $childId)) {
            return;
        }

        // simply return the attributes
        return $attr;
    }

    /**
     * Load's the product relation with the passed parent/child ID.
     *
     * @param integer $parentId The entity ID of the product relation's parent product
     * @param integer $childId  The entity ID of the product relation's child product
     *
     * @return array The product relation
     */
    protected function loadProductRelation($parentId, $childId)
    {
        return $this->getProductRelationAwareProcessor()->loadProductRelation($parentId, $childId);
    }
}
