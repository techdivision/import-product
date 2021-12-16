<?php

/**
 * TechDivision\Import\Product\Repositories\ProductDatetimeRepository
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

use TechDivision\Import\Product\Utils\CacheKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractFinderRepository;

/**
 * Repository implementation to load product datetime attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductDatetimeRepository extends AbstractFinderRepository implements ProductDatetimeRepositoryInterface
{

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::PRODUCT_DATETIMES));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::PRODUCT_DATETIMES_BY_PK_AND_STORE_ID));
    }

    /**
     * Return's the primary key name of the entity.
     *
     * @return string The name of the entity's primary key
     */
    public function getPrimaryKeyName()
    {
        return MemberNames::VALUE_ID;
    }

    /**
     * Return's the finder's entity name.
     *
     * @return string The finder's entity name
     */
    public function getEntityName()
    {
        return CacheKeys::PRODUCT_DATETIME;
    }

    /**
     * Load's and return's the available datetime attributes.
     *
     * @return array The integer attributes
     */
    public function findAll()
    {
        foreach ($this->getFinder(SqlStatementKeys::PRODUCT_DATETIMES)->find() as $result) {
            yield $result;
        }
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
        $params = array(MemberNames::PK => $pk, MemberNames::STORE_ID => $storeId);

        // load the entities and return them
        foreach ($this->getFinder(SqlStatementKeys::PRODUCT_DATETIMES_BY_PK_AND_STORE_ID)->find($params) as $result) {
            yield $result;
        }
    }
}
