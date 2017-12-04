<?php

/**
 * TechDivision\Import\Product\Repositories\CategoryProductRepository
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
use TechDivision\Import\Repositories\AbstractRepository;

/**
 * Repository implementation to load product data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CategoryProductRepository extends AbstractRepository
{

    /**
     * The prepared statement to load the existing category product relations.
     *
     * @var \PDOStatement
     */
    protected $categoryProductStmt;

    /**
     * The prepared statement to load the existing category product relations for the product with the given SKU.
     *
     * @var \PDOStatement
     */
    protected $categoryProductsBySkuStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->categoryProductStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_PRODUCT));
        $this->categoryProductsBySkuStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_PRODUCT_BY_SKU));
    }

    /**
     * Return's the category product relation with the passed category/product ID.
     *
     * @param integer $categoryId The category ID of the category product relation to return
     * @param integer $productId  The product ID of the category product relation to return
     *
     * @return array The category product relation
     */
    public function findOneByCategoryIdAndProductId($categoryId, $productId)
    {

        // prepare the params
        $params = array(
            MemberNames::CATEGORY_ID => $categoryId,
            MemberNames::PRODUCT_ID  => $productId
        );

        // load and return the product category relation with the passed category/product ID
        $this->categoryProductStmt->execute($params);
        return $this->categoryProductStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's the category product relations for the product with the passed SKU.
     *
     * @param string $sku The product SKU to load the category relations for
     *
     * @return array The category product relations for the product with the passed SKU
     */
    public function findAllBySku($sku)
    {

        // prepare the params
        $params = array(MemberNames::SKU => $sku);

        // initialize the array for the category product relations
        $categoryProducts = array();

        // load and return the product category relation for the passed product SKU
        $this->categoryProductsBySkuStmt->execute($params);

        // prepare the result by using the category ID as key
        foreach ($this->categoryProductsBySkuStmt->fetchAll(\PDO::FETCH_ASSOC) as $categoryProduct) {
            $categoryProducts[$categoryProduct[MemberNames::CATEGORY_ID]] = $categoryProduct;
        }

        // return the category product relations
        return $categoryProducts;
    }
}
