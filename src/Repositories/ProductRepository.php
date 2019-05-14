<?php

/**
 * TechDivision\Import\Product\Repositories\ProductRepository
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

use TechDivision\Import\Cache\CacheAdapterInterface;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Repositories\AbstractRepository;
use TechDivision\Import\Connection\ConnectionInterface;
use TechDivision\Import\Repositories\SqlStatementRepositoryInterface;

/**
 * Repository implementation to load product data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductRepository extends AbstractRepository implements ProductRepositoryInterface
{

    /**
     * The cache adapter instance.
     *
     * @var \TechDivision\Import\Cache\CacheAdapterInterface
     */
    protected $cacheAdapter;

    /**
     * The prepared statement to load a product with the passed SKU.
     *
     * @var \PDOStatement
     */
    protected $productStmt;

    /**
     * The prepared statement to load the existing products.
     *
     * @var \PDOStatement
     */
    protected $productsStmt;

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
     * Return's the primary key name of the entity.
     *
     * @return string The name of the entity's primary key
     */
    public function getPrimaryKeyName()
    {
        return MemberNames::ENTITY_ID;
    }

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->productStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT));
        $this->productsStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCTS));
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
     * Return's the available products.
     *
     * @return array The available products
     */
    public function findAll()
    {

        // load and return the available products
        $this->productsStmt->execute();
        return $this->productsStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's the product with the passed SKU.
     *
     * @param string $sku The SKU of the product to return
     *
     * @return array|null The product
     */
    public function findOneBySku($sku)
    {

        // return the cached result if available
        if ($this->cacheAdapter->isCached($sku)) {
            return $this->cacheAdapter->fromCache($sku);
        }

        // if not, try to load the product with the passed SKU
        $this->productStmt->execute(array(MemberNames::SKU => $sku));

        // query whether or not the product is available in the database
        if ($product = $this->productStmt->fetch(\PDO::FETCH_ASSOC)) {
            // prepare the unique cache key for the product
            $cacheKey = $this->cacheAdapter->cacheKey(
                ProductRepositoryInterface::class,
                array($product[$this->getPrimaryKeyName()])
            );
            // add the EAV attribute option value to the cache, register the cache key reference as well
            $this->cacheAdapter->toCache($cacheKey, $product, array($sku => $cacheKey));
            // finally, return it
            return $product;
        }
    }
}
