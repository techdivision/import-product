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
use TechDivision\Import\Cache\CacheAdapterInterface;
use TechDivision\Import\Repositories\AbstractRepository;
use TechDivision\Import\Connection\ConnectionInterface;
use TechDivision\Import\Repositories\SqlStatementRepositoryInterface;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\CacheKeys;

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
     * The cache adapter instance.
     *
     * @var \TechDivision\Import\Cache\CacheAdapterInterface
     */
    protected $cacheAdapter;

    /**
     * The prepared statement to load the existing product varchar attributes with the passed entity/store ID.
     *
     * @var \PDOStatement
     */
    protected $productVarcharsStmt;

    /**
     * The prepared statement to load the existing product varchar attribute with the passed attribute code
     * entity type/store ID.
     *
     * @var \PDOStatement
     */
    protected $productVarcharsByAttributeCodeAndEntityTypeIdAndStoreIdStmt;

    /**
     * The prepared statement to load the existing product varchar attribute with the passed attribute code
     * entity type/store ID as well as the passed value.
     *
     * @var \PDOStatement
     */
    protected $productVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt;

    /**
     * Initialize the repository with the passed connection and utility class name.
     * .
     * @param \TechDivision\Import\Connection\ConnectionInterface               $connection             The connection instance
     * @param \TechDivision\Import\Repositories\SqlStatementRepositoryInterface $sqlStatementRepository The SQL repository instance
     * @param \TechDivision\Import\Cache\CacheAdapterInterface                  $cacheAdapter           The cache adapter instance
     */
    public function __construct(
        ConnectionInterface $connection,
        SqlStatementRepositoryInterface $sqlStatementRepository,
        CacheAdapterInterface $cacheAdapter
    ) {

        // pass the connection the SQL statement repository to the parent class
        parent::__construct($connection, $sqlStatementRepository);

        // set the cache adapter instance
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * Returns the cache adapter instance used to warm the repository.
     *
     * @return \TechDivision\Import\Cache\CacheAdapterInterface The repository's cache adapter instance
     */
    public function getCacheAdapter()
    {
        return $this->cacheAdapter;
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
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->productVarcharsStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_VARCHARS));
        $this->productVarcharsByAttributeCodeAndEntityTypeIdAndStoreIdStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID));
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

        // fetch the values and return them
        while ($record = $this->productVarcharsStmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
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
            ParamNames::ATTRIBUTE_CODE => $attributeCode,
            ParamNames::ENTITY_TYPE_ID => $entityTypeId,
            ParamNames::STORE_ID       => $storeId,
        );

        // load and return the product varchar attributes with the passed primary key/store ID
        $this->productVarcharsByAttributeCodeAndEntityTypeIdAndStoreIdStmt->execute($params);

        // fetch the values and return them
        while ($record = $this->productVarcharsByAttributeCodeAndEntityTypeIdAndStoreIdStmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
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
            ParamNames::ATTRIBUTE_CODE => $attributeCode,
            ParamNames::ENTITY_TYPE_ID => $entityTypeId,
            ParamNames::STORE_ID       => $storeId,
            ParamNames::VALUE          => $value
        );

        // prepare the cache key
        $cacheKey = $this->cacheAdapter->cacheKey(array(SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE => $params));

        // query whether or not the item has already been cached
        if ($this->cacheAdapter->isCached($cacheKey)) {
            return $this->cacheAdapter->fromCache($cacheKey);
        }

        // load and return the product varchar attribute with the passed parameters
        $this->productVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt->execute($params);

        // query whether or not the product varchar value is available in the database
        if ($productVarchar = $this->productVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt->fetch(\PDO::FETCH_ASSOC)) {
            // prepare the unique cache key for the EAV attribute option value
            $uniqueKey = array(CacheKeys::PRODUCT_VARCHAR => $productVarchar[$this->getPrimaryKeyName()]);
            // add the EAV attribute option value to the cache, register the cache key reference as well
            $this->cacheAdapter->toCache($uniqueKey, $productVarchar, array($cacheKey => $uniqueKey));
        }

        // finally, return it
        return $productVarchar;
    }
}
