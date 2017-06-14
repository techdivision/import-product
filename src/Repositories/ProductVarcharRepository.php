<?php

/**
 * TechDivision\Import\Product\Repositories\ProductVarcharRepository
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
 * Repository implementation to load product varchar attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ProductVarcharRepository extends AbstractRepository
{

    /**
     * The prepared statement to load the existing product varchar attribute.
     *
     * @var \PDOStatement
     */
    protected $productVarcharStmt;

    /**
     * The prepared statement to load the existing product varchar attribute with the passed attribute code
     * entity type/store ID as well as the passed value.
     *
     * @var \PDOStatement
     */
    protected $productVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt;

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
        $this->productVarcharStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::PRODUCT_VARCHAR));
        $this->productVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt =
                $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE));
    }

    /**
     * Load's and return's the varchar attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The varchar attribute
     */
    public function findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId)
    {

        // prepare the params
        $params = array(
            MemberNames::STORE_ID      => $storeId,
            MemberNames::ENTITY_ID     => $entityId,
            MemberNames::ATTRIBUTE_ID  => $attributeId
        );

        // load and return the product varchar attribute with the passed store/entity/attribute ID
        $this->productVarcharStmt->execute($params);
        return $this->productVarcharStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Load's and return's the varchar attribute with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     * @param string  $value         The value of the varchar attribute
     *
     * @return array|null The varchar attribute
     */
    public function findOneByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value)
    {

        // prepare the params
        $params = array(
            MemberNames::ATTRIBUTE_CODE => $attributeCode,
            MemberNames::ENTITY_TYPE_ID => $entityTypeId,
            MemberNames::STORE_ID       => $storeId,
            MemberNames::VALUE          => $value
        );

        // load and return the product varchar attribute with the passed parameters
        $this->productVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt->execute($params);
        return $this->productVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
