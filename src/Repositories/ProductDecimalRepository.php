<?php

/**
 * TechDivision\Import\Product\Repositories\ProductDecimalRepository
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
 * Repository implementation to load product decimal attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductDecimalRepository extends AbstractFinderRepository implements ProductDecimalRepositoryInterface
{

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::PRODUCT_DECIMALS));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::PRODUCT_DECIMALS_BY_PK_AND_STORE_ID));
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
        return CacheKeys::PRODUCT_DECIMAL;
    }

    /**
     * Load's and return's the available decimals attributes.
     *
     * @return array The decimal attributes
     */
    public function findAll()
    {
        foreach ($this->getFinder(SqlStatementKeys::PRODUCT_DECIMALS)->find() as $result) {
            yield $result;
        }
    }

    /**
     * Load's and return's the decimal attributes for the passed primary key/store ID.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The decimal attributes
     */
    public function findAllByPrimaryKeyAndStoreId($pk, $storeId)
    {

        // prepare the params
        $params = array(MemberNames::PK => $pk, MemberNames::STORE_ID => $storeId);

        // load the entities and return them
        foreach ($this->getFinder(SqlStatementKeys::PRODUCT_DECIMALS_BY_PK_AND_STORE_ID)->find($params) as $result) {
            yield $result;
        }
    }
}
