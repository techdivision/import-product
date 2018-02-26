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
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Repositories;

use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Repositories\AbstractCachedRepository;

/**
 * Repository implementation to load product data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ProductRepository extends AbstractCachedRepository implements ProductRepositoryInterface
{

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
        if ($this->isCached($sku)) {
            return $this->fromCache($sku);
        }

        // if not, try to load the product with the passed SKU
        $this->productStmt->execute(array(MemberNames::SKU => $sku));

        // query whether or not the product is available in the database
        if ($product = $this->productStmt->fetch(\PDO::FETCH_ASSOC)) {
            // add the product to the cache, register the SKU reference as well
            $this->toCache(
                $product[$this->getPrimaryKeyName()],
                $product,
                array($sku => $product[$this->getPrimaryKeyName()])
            );
            // finally, return it
            return $product;
        }
    }
}
