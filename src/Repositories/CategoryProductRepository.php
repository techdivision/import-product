<?php

/**
 * TechDivision\Import\Product\Repositories\CategoryProductRepository
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Repositories;

use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;

/**
 * Repository implementation to load product data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class CategoryProductRepository extends AbstractRepository implements CategoryProductRepositoryInterface
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

        // load and return the product category relation for the passed product SKU
        $this->categoryProductsBySkuStmt->execute($params);

        // prepare the result by using the category ID as key
        while ($record = $this->categoryProductsBySkuStmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $record[MemberNames::CATEGORY_ID] => $record;
        }
    }
}
