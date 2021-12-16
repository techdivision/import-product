<?php

/**
 * TechDivision\Import\Product\Repositories\ProductRepositoryInterface
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

use TechDivision\Import\Dbal\Repositories\FinderAwareEntityRepositoryInterface;

/**
 * Interface for all product repository implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
interface ProductRepositoryInterface extends FinderAwareEntityRepositoryInterface
{

    /**
     * Return's the available products.
     *
     * @return array The available products
     */
    public function findAll();

    /**
     * Return's the product with the passed SKU.
     *
     * @param string $sku The SKU of the product to return
     *
     * @return array The product
     */
    public function findOneBySku($sku);
}
