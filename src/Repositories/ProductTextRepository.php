<?php

/**
 * TechDivision\Import\Product\Repositories\ProductTextRepository
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

use TechDivision\Import\Product\Utils\ParamNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Repositories\AbstractRepository;

/**
 * Repository implementation to load product text attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ProductTextRepository extends AbstractRepository implements ProductTextRepositoryInterface
{

    /**
     * The prepared statement to load the existing product text attributes with the passed entity/store ID.
     *
     * @var \PDOStatement
     */
    protected $productTextsStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->productTextsStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_TEXTS));
    }

    /**
     * Load's and return's the text attributes with the passed primary key/store ID.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The text attributes
     */
    public function findAllByPrimaryKeyAndStoreId($pk, $storeId)
    {

        // prepare the params
        $params = array(
            ParamNames::PK        => $pk,
            ParamNames::STORE_ID  => $storeId
        );

        // load and return the product text attributes with the passed primary key/store ID
        $this->productTextsStmt->execute($params);
        return $this->productTextsStmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
