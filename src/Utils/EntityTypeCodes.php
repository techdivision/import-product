<?php

/**
 * TechDivision\Import\Product\Utils\EntityTypeCodes
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Utils;

/**
 * Utility class containing the entity type codes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class EntityTypeCodes extends \TechDivision\Import\Utils\EntityTypeCodes
{

    /**
     * Key for the product entity 'catalog_category_product'.
     *
     * @var string
     */
    const CATALOG_CATEGORY_PRODUCT = 'catalog_category_product';

    /**
     * Key for the product entity 'cataloginventory_stock_item'.
     *
     * @var string
     */
    const CATALOGINVENTORY_STOCK_ITEM = 'cataloginventory_stock_item';

    /**
     * Key for the product entity 'cataloginventory_stock_item'.
     *
     * @var string
     */
    const CATALOGINVENTORY_STOCK_ITEM_STATUS = 'cataloginventory_stock_status';
}
