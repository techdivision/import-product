<?php

/**
 * TechDivision\Import\Product\Repositories\StockItemRepository
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

use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;

/**
 * Repository implementation to load stock item data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class StockItemRepository extends AbstractRepository implements StockItemRepositoryInterface
{

    /**
     * The prepared statement to load the existing stock items.
     *
     * @var \PDOStatement
     */
    protected $stockItemStmt;

    /**
     * The prepared statement to load the existing stock items.
     *
     * @var \PDOStatement
     */
    protected $stockItemStatusStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->stockItemStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::STOCK_ITEM));
        $this->stockItemStatusStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::STOCK_ITEM_STATUS));
    }

    /**
     * Load's and return's the stock item with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock item to load
     * @param integer $websiteId The website ID of the stock item to load
     * @param integer $stockId   The stock ID of the stock item to load
     *
     * @return array The stock item
     */
    public function findOneByProductIdAndWebsiteIdAndStockId($productId, $websiteId, $stockId)
    {

        // prepare the params
        $params = array(
            MemberNames::PRODUCT_ID => $productId,
            MemberNames::WEBSITE_ID => $websiteId,
            MemberNames::STOCK_ID   => $stockId
        );

        // load and return the stock status with the passed product/website/stock ID
        $this->stockItemStmt->execute($params);
        return $this->stockItemStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Load's and return's the stock item status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock item to load
     * @param integer $websiteId The website ID of the stock item to load
     * @param integer $stockId   The stock ID of the stock item to load
     *
     * @return array The stock item status
     */
    public function findOneStockStatusByProductIdAndWebsiteIdAndStockId($productId, $websiteId, $stockId)
    {

        // prepare the params
        $params = array(
            MemberNames::PRODUCT_ID => $productId,
            MemberNames::WEBSITE_ID => $websiteId,
            MemberNames::STOCK_ID   => $stockId
        );

        // load and return the stock status with the passed product/website/stock ID
        $this->stockItemStatusStmt->execute($params);
        return $this->stockItemStatusStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
