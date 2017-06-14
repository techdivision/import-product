<?php

/**
 * TechDivision\Import\Product\Repositories\ProductWebsiteRepository
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
 * Repository implementation to load product website data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ProductWebsiteRepository extends AbstractRepository
{

    /**
     * The prepared statement to load the existing product website relations.
     *
     * @var \PDOStatement
     */
    protected $productWebsiteStmt;

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
        $this->productWebsiteStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::PRODUCT_WEBSITE));
    }

    /**
     * Load's and return's the product website relation with the passed product and website ID.
     *
     * @param string $productId The product ID of the relation
     * @param string $websiteId The website ID of the relation
     *
     * @return array The product website
     */
    public function findOneByProductIdAndWebsite($productId, $websiteId)
    {

        // prepare the params
        $params = array(
            MemberNames::PRODUCT_ID => $productId,
            MemberNames::WEBSITE_ID => $websiteId
        );

        // load and return the product with the passed product/website ID
        $this->productWebsiteStmt->execute($params);
        return $this->productWebsiteStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
