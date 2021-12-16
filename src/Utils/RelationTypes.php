<?php

/**
 * TechDivision\Import\Product\Utils\RelationTypes
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Utils;

/**
 * Utility class containing the available product relation types.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class RelationTypes
{

    /**
     * Type for a generic 'product_relation' that will be used for variants + bundles.
     *
     * @var integer
     */
    const PRODUCT_RELATION = 'product_relation';

    /**
     * Type for 'variant_super_link'.
     *
     * @var integer
     */
    const VARIANT_SUPER_LINK = 'variant_super_link';

    /**
     * Type for 'variant_super_attribute'.
     *
     * @var integer
     */
    const VARIANT_SUPER_ATTRIBUTE = 'variant_super_attribute';

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
