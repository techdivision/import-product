<?php

/**
 * TechDivision\Import\Product\Repositories\ProductRelationRepository
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
 * Repository implementation to load product relation data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductRelationRepository extends AbstractRepository implements ProductRelationRepositoryInterface
{

    /**
     * The prepared statement to load an existing product relation.
     *
     * @var \PDOStatement
     */
    protected $productRelationStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->productRelationStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_RELATION));
    }

    /**
     * Load's the product relation with the passed parent/child ID.
     *
     * @param integer $parentId The entity ID of the product relation's parent product
     * @param integer $childId  The entity ID of the product relation's child product
     *
     * @return array The product relation
     */
    public function findOneByParentIdAndChildId($parentId, $childId)
    {

        // initialize the params
        $params = array(
            MemberNames::PARENT_ID => $parentId,
            MemberNames::CHILD_ID  => $childId
        );

        // load and return the product relation with the passed parent/child ID
        $this->productRelationStmt->execute($params);
        return $this->productRelationStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
