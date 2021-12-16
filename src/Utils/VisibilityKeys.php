<?php

/**
 * TechDivision\Import\Product\Utils\ColumnKeys
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Utils;

/**
 * Utility class containing the available visibility keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class VisibilityKeys
{

    /**
     * Key for 'Not Visible Individually'.
     *
     * @var integer
     */
    const VISIBILITY_NOT_VISIBLE = 1;

    /**
     * Key for 'Catalog'.
     *
     * @var integer
     */
    const VISIBILITY_IN_CATALOG = 2;

    /**
     * Key for 'Search'.
     *
     * @var integer
     */
    const VISIBILITY_IN_SEARCH = 3;

    /**
     * Key for 'Catalog, Search'.
     *
     * @var integer
     */
    const VISIBILITY_BOTH = 4;

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }
}
