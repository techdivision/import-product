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
class ProductRepository extends AbstractRepository
{

    /**
     * The prepared statement to load the existing products.
     *
     * @var \PDOStatement
     */
    protected $productStmt;

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
        $this->productStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::PRODUCT));
    }

    /**
     * Return's the product with the passed SKU.
     *
     * @param string $sku The SKU of the product to return
     *
     * @return array The product
     */
    public function findOneBySku($sku)
    {
        // load and return the product with the passed SKU
        $this->productStmt->execute(array(MemberNames::SKU => $sku));
        return $this->productStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
