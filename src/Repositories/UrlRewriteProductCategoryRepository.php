<?php

/**
 * TechDivision\Import\Product\Repositories\UrlRewriteProductCategoryRepository
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
 * Repository implementation to load URL rewrite product category relation data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlRewriteProductCategoryRepository extends AbstractRepository
{

    /**
     * The prepared statement to load an existing URL rewrite product category relation.
     *
     * @var \PDOStatement
     */
    protected $urlRewriteProductCategoryStmt;

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
        $this->urlRewriteProductCategoryStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::URL_REWRITE_PRODUCT_CATEGORY_BY_PRODUCT_ID_AND_CATEGORY_ID));
    }

    /**
     * Return's the URL rewrite product category relation for the passed
     * product and category ID.
     *
     * @param integer $productId  The product ID to load the URL rewrite product category relation for
     * @param integer $categoryId The category ID to load the URL rewrite product category relation for
     *
     * @return array|false The URL rewrite product category relations
     */
    public function findOneByProductIdAndCategoryId($productId, $categoryId)
    {

        // initialize the parameters
        $params = array(
            MemberNames::PRODUCT_ID => $productId,
            MemberNames::CATEGORY_ID => $categoryId
        );
        // load and return the URL rewrite product category relation
        $this->urlRewriteProductCategoryStmt->execute($params);
        return $this->urlRewriteProductCategoryStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
