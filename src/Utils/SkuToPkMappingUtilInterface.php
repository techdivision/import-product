<?php

/**
 * TechDivision\Import\Product\Utils\SkuToPkMappingUtilInterface
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

use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * Utility class for edition based primary key handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
interface SkuToPkMappingUtilInterface
{

    /**
     * Returns the primary key member name for the actual Magento edition.
     *
     * @return string The primary key member name
     * @throws \Exception Is thrown if the edition is not supported/available
     */
    public function getSkuToPkMappingKey();

    /**
     * Returns the array with the SKU => PK mapping for the given serial and registry processor.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor with the actual import status
     * @param string                                                   $serial            The serial of the actual import
     *
     * @return array The array with the SKU => PK mapping
     */
    public function getSkuToPkMapping(RegistryProcessorInterface $registryProcessor, $serial);

    /**
     * Adds the passed SKU => PK mapping to the passed registry processor.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor with the actual import status
     * @param string                                                   $serial            The serial of the actual import
     * @param array                                                    $skuToPkMapping    The SKU => PK mapping to add
     *
     * @return void
     */
    public function setSkuToPkMapping(RegistryProcessorInterface $registryProcessor, $serial, array $skuToPkMapping);

    /**
     * Returns the array with the SKU => PK mapping for the given serial and registry processor.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor with the actual import status
     * @param string                                                   $serial            The serial of the actual import
     *
     * @return array The array with the PK => SKU mapping
     * @see \TechDivision\Import\Product\Utils\SkuToPkMappingUtilInterface::getSkuToPkMapping()
     */
    public function getInvertedSkuToPkMapping(RegistryProcessorInterface $registryProcessor, $serial);
}
