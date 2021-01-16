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

use TechDivision\Import\Product\Utils\CacheKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractFinderRepository;

/**
 * Repository implementation to load product varchar attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductVarcharRepository extends AbstractFinderRepository implements ProductVarcharRepositoryInterface
{

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::PRODUCT_VARCHARS));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::PRODUCT_VARCHARS_BY_PK_AND_STORE_ID));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_PK));
    }

    /**
     * Return's the primary key name of the entity.
     *
     * @return string The name of the entity's primary key
     */
    public function getPrimaryKeyName()
    {
        return MemberNames::VALUE_ID;
    }

    /**
     * Return's the finder's entity name.
     *
     * @return string The finder's entity name
     */
    public function getEntityName()
    {
        return CacheKeys::PRODUCT_VARCHAR;
    }

    /**
     * Load's and return's the available varchar attributes.
     *
     * @return array The varchar attributes
     */
    public function findAll()
    {

        // load the entities and return them
        foreach ($this->getFinder(SqlStatementKeys::PRODUCT_VARCHARS)->find() as $result) {
            yield $result;
        }
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
        $params = array(MemberNames::PK => $pk, MemberNames::STORE_ID => $storeId);

        // load the entities and return them
        foreach ($this->getFinder(SqlStatementKeys::PRODUCT_VARCHARS_BY_PK_AND_STORE_ID)->find($params) as $result) {
            yield $result;
        }
    }

    /**
     * Load's and return's the varchar attributes with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     *
     * @return array The varchar attributes
     */
    public function findAllByAttributeCodeAndEntityTypeIdAndStoreId($attributeCode, $entityTypeId, $storeId)
    {

        // prepare the params
        $params = array(
            MemberNames::ATTRIBUTE_CODE => $attributeCode,
            MemberNames::ENTITY_TYPE_ID => $entityTypeId,
            MemberNames::STORE_ID       => $storeId,
        );

        // load the entities and return them
        foreach ($this->getFinder(SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID)->find($params) as $result) {
            yield $result;
        }
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

        // load and return the entity
        return $this->getFinder(SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE)->find($params);
    }

    /**
     * Load's and return's the varchar attribute with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     * @param string  $pk            The primary key of the product
     *
     * @return array|null The varchar attribute
     */
    public function findOneByAttributeCodeAndEntityTypeIdAndStoreIdAndPk($attributeCode, $entityTypeId, $storeId, $pk)
    {

        // prepare the params
        $params = array(
            MemberNames::ATTRIBUTE_CODE => $attributeCode,
            MemberNames::ENTITY_TYPE_ID => $entityTypeId,
            MemberNames::STORE_ID       => $storeId,
            MemberNames::PK             => $pk
        );

        // load and return the entity
        return $this->getFinder(SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_PK)->find($params);
    }
}
