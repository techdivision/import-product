<?php

/**
 * TechDivision\Import\Product\Repositories\ProductIntRepository
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
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Repositories;

use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Repositories\AbstractRepository;

/**
 * Repository implementation to load product integer attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ProductIntRepository extends AbstractRepository
{

    /**
     * The prepared statement to load the existing product integer attribute.
     *
     * @var \PDOStatement
     */
    protected $productIntStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // load the utility class name
        $utilityClassName = $this->getUtilityClassName();

        // initialize the prepared statements
        $this->productIntStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::PRODUCT_INT));
    }

    /**
     * Load's and return's the integer attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The integer attribute
     */
    public function findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId)
    {

        // prepare the params
        $params = array(
            MemberNames::STORE_ID      => $storeId,
            MemberNames::ENTITY_ID     => $entityId,
            MemberNames::ATTRIBUTE_ID  => $attributeId
        );

        // load and return the product integer attribute with the passed store/entity/attribute ID
        $this->productIntStmt->execute($params);
        return $this->productIntStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
