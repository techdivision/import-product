<?php

/**
 * TechDivision\Import\Product\Utils\RegistryKeys
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
 * Utility class containing the unique registry keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/RegistryKeys.php
 * @link      http://www.techdivision.com
 */
class RegistryKeys extends \TechDivision\Import\Utils\RegistryKeys
{

    /**
     * Key for the registry entry containing the preloaded SKU => entity ID mapping.
     *
     * @var string
     */
    const PRE_LOADED_ENTITY_IDS = 'preLoadedEntityIds';

    /**
     * The key for the registry entry containing the processed SKUs.
     *
     * @var string
     */
    const PROCESSED_SKUS = 'processed_skus';

    /**
     * Key for the registry entry containing the SKU => primary entity ID mapping.
     * string
     */
    const PRIMARY_SKU_TO_PK_MAPPINGS = 'primarySkuPkMappings';
    
    /**
     * Key for the registry entry containing the SKU => primary row ID mapping.
     * 
     * string
     */
    const PRIMARY_SKU_TO_ROW_PK_MAPPINGS = 'primarySkuRowPkMappings';
}
