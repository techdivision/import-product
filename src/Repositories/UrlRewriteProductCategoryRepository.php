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

    protected $urlRewriteProductCategoriesBySkuStmt;

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
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::URL_REWRITE_PRODUCT_CATEGORY));
        $this->urlRewriteProductCategoriesBySkuStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::URL_REWRITE_PRODUCT_CATEGORIES_BY_SKU));
    }

    /**
     * Return's the URL rewrite product category relation for the passed
     * URL rewrite ID.
     *
     * @param integer $urlRewriteId The URL rewrite ID to load the URL rewrite product category relation for
     *
     * @return array|false The URL rewrite product category relation
     */
    public function load($urlRewriteId)
    {

        // initialize the parameters
        $params = array(MemberNames::URL_REWRITE_ID => $urlRewriteId);

        // load and return the URL rewrite product category relation
        $this->urlRewriteProductCategoryStmt->execute($params);
        return $this->urlRewriteProductCategoryStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's an array with the URL rewrite product category relations for the passed SKU.
     *
     * @param string $sku The SKU to load the URL rewrite product category relations for
     *
     * @return array The URL rewrite product category relations
     */
    public function findAllBySku($sku)
    {

        // initialize the params
        $params = array(MemberNames::SKU => $sku);

        // load and return the URL rewrite product category relations for the passed SKU
        $this->urlRewriteProductCategoriesBySkuStmt->execute($params);
        return $this->urlRewriteProductCategoriesBySkuStmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
