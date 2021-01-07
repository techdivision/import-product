<?php

/**
 * TechDivision\Import\Product\Services\ProductRelationAwareProcessorInterface
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

namespace TechDivision\Import\Product\Services;

/**
 * Interface for a product bunch processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
interface ProductRelationAwareProcessorInterface
{

    /**
     * Return's the repository to access product relations.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductRelationRepositoryInterface The repository instance
     */
    public function getProductRelationRepository();

    /**
     * Return's the action with the product relation CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductRelationAction();

    /**
     * Load's the product relation with the passed parent/child ID.
     *
     * @param integer $parentId The entity ID of the product relation's parent product
     * @param integer $childId  The entity ID of the product relation's child product
     *
     * @return array The product relation
     */
    public function loadProductRelation($parentId, $childId);

    /**
     * Persist's the passed product relation data and return's the ID.
     *
     * @param array       $productRelation The product relation data to persist
     * @param string|null $name            The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductRelation($productRelation, $name = null);
}
