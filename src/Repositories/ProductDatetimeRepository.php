<?php

/**
 * TechDivision\Import\Product\Repositories\ProductDatetimeRepository
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

use TechDivision\Import\Product\Utils\ParamNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Repositories\AbstractRepository;

/**
 * Repository implementation to load product datetime attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductDatetimeRepository extends AbstractRepository implements ProductDatetimeRepositoryInterface
{

    /**
     * The prepared statement to load the existing product datetime attributes with the passed entity/store ID.
     *
     * @var \PDOStatement
     */
    protected $productDatetimesStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->productDatetimesStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_DATETIMES));
    }

    /**
     * Load's and return's the datetime attributes for the passed primary key/store ID.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The datetime attributes
     */
    public function findAllByPrimaryKeyAndStoreId($pk, $storeId)
    {

        // prepare the params
        $params = array(
            ParamNames::PK        => $pk,
            ParamNames::STORE_ID  => $storeId
        );

        // load and return the product datetime attributes with the passed primary key/store ID
        $this->productDatetimesStmt->execute($params);

        // fetch the values and return them
        while ($record = $this->productDatetimesStmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
        }
    }
}
