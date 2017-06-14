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

use TechDivision\Import\Repositories\AbstractRepository;
use TechDivision\Import\Product\Utils\MemberNames;

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
    protected $productCategoryStmt;

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
        $this->productCategoryStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::CATEGORY_PRODUCT));
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
        $this->productCategoryStmt->execute($params);
        return $this->productCategoryStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
