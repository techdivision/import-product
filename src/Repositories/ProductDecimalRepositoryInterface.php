<?php

/**
 * TechDivision\Import\Product\Repositories\ProductDecimalRepositoryInterface
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

use TechDivision\Import\Dbal\Repositories\FinderAwareRepositoryInterface;

/**
 * Interface for repositories providing functionality to load product decimal attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
interface ProductDecimalRepositoryInterface extends FinderAwareRepositoryInterface
{

    /**
     * Load's and return's the available decimals attributes.
     *
     * @return array The decimal attributes
     */
    public function findAll();

    /**
     * Load's and return's the decimal attributes for the passed primary key/store ID.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The decimal attributes
     */
    public function findAllByPrimaryKeyAndStoreId($pk, $storeId);
}
