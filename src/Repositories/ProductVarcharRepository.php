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
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Repositories;

use TechDivision\Import\Product\Utils\ParamNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Repositories\AbstractRepository;

/**
 * Repository implementation to load product varchar attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductVarcharRepository extends AbstractRepository implements ProductVarcharRepositoryInterface
{

    /**
     * The prepared statement to load the existing product varchar attributes with the passed entity/store ID.
     *
     * @var \PDOStatement
     */
    protected $productVarcharsStmt;

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

        // initialize the prepared statements
        $this->productVarcharsStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_VARCHARS));
        $this->productVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt =
                $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE));
    }

    /**
     * Load's and return's the varchar attributes with the passed primary key/store ID.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The varchar attributes
     */
    public function findAllByPrimaryKeyAndStoreId($pk, $storeId)
    {

        // prepare the params
        $params = array(
            ParamNames::PK        => $pk,
            ParamNames::STORE_ID  => $storeId
        );

        // load and return the product varchar attributes with the passed primary key/store ID
        $this->productVarcharsStmt->execute($params);
        return $this->productVarcharsStmt->fetchAll(\PDO::FETCH_ASSOC);
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
            ParamNames::ATTRIBUTE_CODE => $attributeCode,
            ParamNames::ENTITY_TYPE_ID => $entityTypeId,
            ParamNames::STORE_ID       => $storeId,
            ParamNames::VALUE          => $value
        );

        // load and return the product varchar attribute with the passed parameters
        $this->productVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt->execute($params);
        return $this->productVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
