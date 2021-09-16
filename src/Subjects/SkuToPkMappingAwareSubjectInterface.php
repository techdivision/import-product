<?php

/**
 * TechDivision\Import\Product\Subjects\SkuToPkMappingAwareSubjectInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Subjects;

use TechDivision\Import\Subjects\SubjectInterface;

/**
 * The subject implementation that handles the business logic to persist products.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
interface SkuToPkMappingAwareSubjectInterface extends SubjectInterface
{

    /**
     * Sets the passed SKU => PK mappings to the implementing instance.
     *
     * @param array $skuToPkMappings The array with the SKU => PK mappings
     *
     * @return void
     */
    public function setSkuToPkMappings(array $skuToPkMappings);

    /**
     * Returns the passed SKU => PK mappings from the implementing instance.
     *
     * @return array The array with the SKU => PK mappings
     */
    public function getSkuToPkMappings();

    /**
     * Returns the inverted SKU => PK mappings from the implementing instance.
     *
     * @return array The array with the inverted mappings
     * @see \TechDivision\Import\Product\Subjects\SkuToPkMappingAwareSubjectInterface::getSkuToPkMappings()
     */
    public function getPkToSkuMappings();

    /**
     * Adds the passed SKU => PK mapping to the implementing instance.
     *
     * @param string  $sku The SKU to map
     * @param integer $pk  The PK to be mapped
     *
     * @return void
     */
    public function addSkuToPkMapping($sku, $pk);

    /**
     * Returns the PK for the passed SKU, if available.
     *
     * @param string $sku The SKU to return the PK for
     *
     * @return integer|null The PK for the given SKU
     */
    public function getSkuToPkMapping($sku);
}
