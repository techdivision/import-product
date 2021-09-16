<?php

/**
 * TechDivision\Import\Product\Utils\Mappings\EntityTypeCodeMapper
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Utils\Mappings;

use TechDivision\Import\Product\Utils\EntityTypeCodes;

/**
 * Mapper implementation for entity type codes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class EntityTypeCodeMapper extends \TechDivision\Import\Utils\Mappings\EntityTypeCodeMapper
{

    /**
     * Construct a new command name to entity type code to mapping instance.
     *
     * @param array $mappings The array with the command name to entity type code mappings
     * @link http://www.php.net/manual/en/arrayobject.construct.php
     */
    public function __construct(array $mappings = array())
    {

        // merge the entity type code mappings with the passed ones
        $mergedMappings = array_merge(
            array(EntityTypeCodes::CATALOG_CATEGORY_PRODUCT => EntityTypeCodes::CATALOG_PRODUCT),
            $mappings
        );

        // initialize the parent class with the merged entity type code mappings
        parent::__construct($mergedMappings);
    }

    /**
     * Map the passed entity type code.
     *
     * @param string $value The entity type code to map
     *
     * @return string The mapped entity type code
     */
    public function map(string $value) : string
    {
        return isset($this[$value]) ? $this[$value] : $value;
    }
}
